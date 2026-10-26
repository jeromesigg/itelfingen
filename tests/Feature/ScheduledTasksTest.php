<?php

namespace Tests\Feature;

use App\Mail\MonthlyMail;
use App\Mail\WeeklyMail;
use App\Models\Application;
use App\Models\BexioToken;
use App\Models\Event;
use App\Models\Newsletter;
use App\Notifications\ApplicationInvoiceNotification;
use App\Notifications\EventFeedbackNotification;
use App\Notifications\EventLastInfosNotification;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use jeremykenedy\Slack\Laravel\Facade as Slack;
use PHPUnit\Framework\Attributes\DataProvider;
use Revolution\Google\Sheets\Facades\Sheets;
use Tests\Support\BookingScenario;
use Tests\TestCase;

/**
 * Geplante Tasks (routes/console.php): täglich 04:00, täglich 19:00, wöchentlich, monatlich.
 * Bexio, Glutz (Türcodes), Slack und Google Sheets sind gefakt.
 */
class ScheduledTasksTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
        Mail::fake();
        BexioToken::create(['access_token' => 'token', 'refresh_token' => 'refresh', 'expires_at' => now()->addHour()]);
        Http::fake([
            'api.bexio.com/2.0/contact/search' => Http::response([]),
            'api.bexio.com/2.0/contact' => Http::response(['id' => 77]),
            'api.bexio.com/2.0/kb_invoice' => Http::response(['id' => 33]),
            'api.bexio.com/*' => Http::response(['id' => 33, 'network_link' => 'https://bexio.test/rechnung']),
            'glutz.test*' => Http::response(['result' => true]),
        ]);
        Slack::shouldReceive('to')->andReturnSelf()->byDefault();
        Slack::shouldReceive('send')->byDefault();
        Sheets::shouldReceive('spreadsheet->sheet->append')->byDefault();
    }

    public static function schedule(): array
    {
        return [
            'täglich' => ['task:daily', '0 4 * * *'],
            'abends' => ['task:daily-evening', '0 19 * * *'],
            'wöchentlich' => ['task:weekly', '0 0 * * 0'],
            'monatlich' => ['task:monthly', '0 2 23 * *'],
        ];
    }

    #[DataProvider('schedule')]
    public function test_task_is_scheduled(string $command, string $expression): void
    {
        $events = collect(app(Schedule::class)->events())->filter(fn ($e) => str_contains((string) $e->command, $command.'"') || str_ends_with((string) $e->command, $command));

        $this->assertCount(1, $events, "$command ist nicht eingeplant");
        $this->assertSame($expression, $events->first()->expression);
    }

    public function test_queue_worker_runs_every_minute_without_overlapping(): void
    {
        $workers = collect(app(Schedule::class)->events())->filter(fn ($e) => str_contains((string) $e->command, 'queue:work --stop-when-empty'));

        $this->assertCount(1, $workers);
        $this->assertSame('* * * * *', $workers->first()->expression);
        $this->assertTrue($workers->first()->withoutOverlapping);
    }

    /** @return array<string, array{0: string}> */
    public static function commands(): array
    {
        return array_map(fn (array $task) => [$task[0]], self::schedule());
    }

    #[DataProvider('commands')]
    public function test_task_runs_without_data(string $command): void
    {
        // Leere Datenbank (nur Stammdaten): kein Task darf abstürzen.
        $this->artisan($command)->assertSuccessful();
    }

    public function test_daily_sends_last_infos_two_weeks_before_stay(): void
    {
        $s = BookingScenario::create();
        $soon = Event::factory()->confirmed()->create(['code' => 1234, 'start_date' => today()->addWeek(), 'end_date' => today()->addWeek()->addDays(2)]);
        $noCode = Event::factory()->confirmed()->create(['start_date' => today()->addWeek(), 'end_date' => today()->addWeek()->addDays(2)]);
        $later = Event::factory()->confirmed()->create(['code' => 1234, 'start_date' => today()->addWeeks(3), 'end_date' => today()->addWeeks(3)->addDay()]);
        $new = Event::factory()->create(['code' => 1234, 'start_date' => today()->addWeek(), 'end_date' => today()->addWeek()->addDay()]);

        $this->artisan('task:daily')->assertSuccessful();

        Notification::assertSentTo($soon, EventLastInfosNotification::class);
        foreach ([$noCode, $later, $new, $s->confirmedEvent] as $other) {
            Notification::assertNotSentTo($other, EventLastInfosNotification::class);
        }
        $this->assertTrue($soon->fresh()->last_info);
        // Checkliste wird angelegt, falls es noch keine gibt
        $this->assertSame(1, $soon->event_rooms()->count());

        // Zweiter Lauf: keine zweite Mail, keine doppelte Checkliste
        $this->artisan('task:daily')->assertSuccessful();
        Notification::assertSentToTimes($soon, EventLastInfosNotification::class, 1);
        $this->assertSame(1, $soon->event_rooms()->count());
    }

    public function test_daily_sends_invoice_for_applications_older_than_two_weeks(): void
    {
        $old = Application::factory()->create(['created_at' => now()->subWeeks(3)]);
        $recent = Application::factory()->create(['created_at' => now()->subWeek()]);
        $refused = Application::factory()->create(['created_at' => now()->subWeeks(3), 'refuse' => true]);
        Sheets::shouldReceive('spreadsheet->sheet->append')->once();

        $this->artisan('task:daily')->assertSuccessful();

        $old->refresh();
        $this->assertSame(77, $old->bexio_user_id);
        $this->assertSame(33, $old->bexio_invoice_id);
        $this->assertTrue((bool) $old->invoice_send);
        Http::assertSent(fn (Request $r) => $r->url() === 'https://api.bexio.com/2.0/kb_invoice'
            && $r['contact_id'] === 77 && $r['positions'][0]['article_id'] === 1300);
        Notification::assertSentTo($old, ApplicationInvoiceNotification::class);
        $this->assertTrue(Newsletter::where('email', $old->email)->sole()->members);

        foreach ([$recent, $refused] as $other) {
            $this->assertFalse((bool) $other->fresh()->invoice_send);
            Notification::assertNotSentTo($other, ApplicationInvoiceNotification::class);
        }
    }

    public function test_daily_announces_next_booking_in_slack(): void
    {
        $event = Event::factory()->confirmed()->create(['start_date' => today()->addDays(2), 'end_date' => today()->addDays(4)]);
        Event::factory()->create(['start_date' => today()->addDays(2), 'end_date' => today()->addDays(4)]);
        Slack::shouldReceive('send')->once()->withArgs(fn ($text) => str_contains($text, $event->name));

        $this->artisan('task:daily')->assertSuccessful();
    }

    public function test_daily_deletes_door_code_users_three_days_after_stay(): void
    {
        $done = Event::factory()->confirmed()->create(['glutz_user_id' => '501', 'start_date' => today()->subDays(8), 'end_date' => today()->subDays(5)]);
        $recent = Event::factory()->confirmed()->create(['glutz_user_id' => '502', 'start_date' => today()->subDays(3), 'end_date' => today()->subDay()]);

        $this->artisan('task:daily')->assertSuccessful();

        $this->assertNull($done->fresh()->glutz_user_id);
        $this->assertSame('502', $recent->fresh()->glutz_user_id);
        Http::assertSent(fn (Request $r) => str_starts_with($r->url(), 'http://glutz.test') && $r['method'] === 'eAccess.deleteUser' && $r['params'] === ['501']);
    }

    public function test_evening_sends_feedback_mails_once(): void
    {
        $ended = Event::factory()->confirmed()->create(['start_date' => today()->subDays(2), 'end_date' => today()]);
        $endsTomorrow = Event::factory()->confirmed()->create(['start_date' => today()->subDay(), 'end_date' => today()->addDay()]);
        $future = Event::factory()->confirmed()->create(['start_date' => today()->addDays(3), 'end_date' => today()->addDays(5)]);
        $cancelled = Event::factory()->cancelled()->create(['start_date' => today()->subDays(2), 'end_date' => today()]);

        $this->artisan('task:daily-evening')->assertSuccessful();
        $this->artisan('task:daily-evening')->assertSuccessful();

        Notification::assertSentToTimes($ended, EventFeedbackNotification::class, 1);
        Notification::assertSentToTimes($endsTomorrow, EventFeedbackNotification::class, 1);
        Notification::assertNotSentTo($future, EventFeedbackNotification::class);
        Notification::assertNotSentTo($cancelled, EventFeedbackNotification::class);
    }

    public function test_weekly_mail_lists_open_work(): void
    {
        $s = BookingScenario::create();
        $noCode = Event::factory()->confirmed()->create(['start_date' => today()->addMonth(), 'end_date' => today()->addMonth()->addDay()]);

        $this->artisan('task:weekly')->assertSuccessful();

        Mail::assertSent(WeeklyMail::class, function (WeeklyMail $mail) use ($s, $noCode) {
            $lists = (fn () => $this->events)->call($mail)->mapWithKeys(fn ($list) => [$list['text'] => collect($list['events'])->pluck('id')]);
            $contacts = (fn () => $this->contacts)->call($mail);

            return $lists['Folgende Buchungen wurden noch nicht bearbeitet']->contains($s->event->id)
                && $lists['Folgende Buchungen haben noch keinen Tür-Code.']->contains($noCode->id)
                && $lists['Folgende Buchungen haben noch keine Rechnung erhalten.']->contains($s->pastEvent->id)
                && $contacts->pluck('id')->all() === [$s->contact->id];
        });
    }

    public function test_monthly_mail_lists_bookings_of_next_six_weeks(): void
    {
        $soon = Event::factory()->confirmed()->create(['start_date' => today()->addWeeks(2), 'end_date' => today()->addWeeks(2)->addDay()]);
        // Zu spät bzw. storniert: dürfen nicht in der Liste erscheinen
        Event::factory()->confirmed()->create(['start_date' => today()->addWeeks(8), 'end_date' => today()->addWeeks(8)->addDay()]);
        Event::factory()->cancelled()->create(['start_date' => today()->addWeeks(2), 'end_date' => today()->addWeeks(2)->addDay()]);
        // Storniert, aber die Reinigungsfirma ist schon informiert: muss in der Liste bleiben
        $cancelledInformed = Event::factory()->cancelled()->create(['cleaning_mail' => true, 'start_date' => today()->addWeeks(3), 'end_date' => today()->addWeeks(3)->addDay()]);

        $this->artisan('task:monthly')->assertSuccessful();

        Mail::assertSent(MonthlyMail::class, function (MonthlyMail $mail) use ($soon, $cancelledInformed) {
            /** @var Collection $events */
            $events = (fn () => $this->events)->call($mail);

            return $events->pluck('id')->all() === [$soon->id, $cancelledInformed->id];
        });
    }

    public function test_weekly_mail_renders_open_bookings_and_contacts(): void
    {
        $s = BookingScenario::create();

        $this->artisan('task:weekly')->assertSuccessful();

        Mail::assertSent(WeeklyMail::class, function (WeeklyMail $mail) use ($s) {
            $html = $mail->render();

            return str_contains($html, 'Folgende Anfragen sind noch offen')
                && str_contains($html, e($s->contact->subject))
                && str_contains($html, route('admin.events.edit', $s->event->id));
        });
    }

    public function test_monthly_mail_renders_bookings(): void
    {
        $soon = Event::factory()->confirmed()->create(['name' => 'Testgruppe Monatsmail', 'start_date' => today()->addWeeks(2), 'end_date' => today()->addWeeks(2)->addDay()]);

        $this->artisan('task:monthly')->assertSuccessful();

        // Reinigung am Tag nach der Abreise, ohne Namen der Gäste
        $cleaningDay = today()->addWeeks(2)->addDays(2)->format('d.m');
        Mail::assertSent(MonthlyMail::class, fn (MonthlyMail $mail) => str_contains($mail->render(), $cleaningDay)
            && ! str_contains($mail->render(), e($soon->name)));
    }
}
