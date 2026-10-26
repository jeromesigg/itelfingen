<?php

namespace Tests\Feature;

use App\Enums\BookingState;
use App\Enums\ContractState;
use App\Models\Event;
use App\Models\User;
use App\Notifications\EventOfferSendNotification;
use App\Services\BookingChecks;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Offene Arbeiten an Buchungen (BookingChecks): Aufgabenliste des Dashboards.
 * Die Listen des Wochen-Mails prüft ScheduledTasksTest.
 */
class BookingChecksTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->travelTo('2026-03-02 10:00:00');
    }

    /**
     * @return array<int, array{title: string, subtitle: string, urgent: bool, link: string}>
     */
    private function tasks(): array
    {
        return app(BookingChecks::class)->dashboardTasks()->all();
    }

    /**
     * @return list<string>
     */
    private function titlesFor(Event $event): array
    {
        return collect($this->tasks())
            ->filter(fn (array $task) => $task['link'] === route('admin.events.edit', $event->id))
            ->pluck('title')->values()->all();
    }

    /** @return array<string, array{0: string, 1: bool}> */
    public static function doorCodeUrgency(): array
    {
        return [
            'Anreise in 5 Tagen' => ['2026-03-07', true],
            'Anreise in 14 Tagen' => ['2026-03-16', true],
            'Anreise in 2 Monaten' => ['2026-05-02', false],
        ];
    }

    #[DataProvider('doorCodeUrgency')]
    public function test_missing_door_code_is_urgent_two_weeks_before_arrival(string $start, bool $urgent): void
    {
        $event = Event::factory()->confirmed()->create(['start_date' => $start, 'end_date' => $start, 'cleaning_mail' => true, 'user_id' => User::factory()->verwalter()->create()->id]);

        $tasks = $this->tasks();

        $this->assertCount(1, $tasks);
        $this->assertSame('Türcode fehlt', $tasks[0]['title']);
        $this->assertSame($urgent, $tasks[0]['urgent']);
        $this->assertSame(route('admin.events.edit', $event->id), $tasks[0]['link']);
    }

    public function test_confirmed_booking_with_everything_done_has_no_task(): void
    {
        Event::factory()->confirmed()->create(['start_date' => '2026-03-07', 'end_date' => '2026-03-08', 'code' => '4711', 'cleaning_mail' => true, 'user_id' => User::factory()->verwalter()->create()->id]);

        $this->assertSame([], $this->tasks());
    }

    public function test_cancelled_booking_has_no_task(): void
    {
        Event::factory()->cancelled()->create(['start_date' => '2026-03-07', 'end_date' => '2026-03-08']);

        $this->assertSame([], $this->tasks());
    }

    public function test_created_but_unsent_offer_asks_to_send_it(): void
    {
        $event = Event::factory()->create(['contract_status_id' => ContractState::AngebotErstellt]);

        $this->assertSame(['Angebot versenden'], $this->titlesFor($event));
    }

    public function test_confirmed_booking_without_responsible_user_asks_to_assign_one(): void
    {
        $withoutUser = Event::factory()->confirmed()->create(['start_date' => '2026-08-01', 'end_date' => '2026-08-02']);
        $withUser = Event::factory()->confirmed()->create(['start_date' => '2026-08-01', 'end_date' => '2026-08-02', 'user_id' => User::factory()->verwalter()->create()->id]);

        $this->assertSame(['Person zuweisen'], $this->titlesFor($withoutUser));
        $this->assertSame([], $this->titlesFor($withUser));
    }

    public function test_offer_sent_more_than_two_months_ago_asks_to_follow_up(): void
    {
        $stale = Event::factory()->create(['contract_status_id' => ContractState::AngebotVersendet]);
        $recent = Event::factory()->create(['contract_status_id' => ContractState::AngebotVersendet]);
        $this->offerSentAt($stale, '2025-12-20');
        $this->offerSentAt($recent, '2026-02-01');

        $this->assertSame(['Angebot nachfassen'], $this->titlesFor($stale));
        $this->assertStringContainsString('versendet 20.12.2025', collect($this->tasks())->firstWhere('title', 'Angebot nachfassen')['subtitle']);
        $this->assertSame([], $this->titlesFor($recent));
    }

    public function test_own_booking_with_old_offer_is_not_followed_up(): void
    {
        $event = Event::factory()->create(['event_status_id' => BookingState::Eigene, 'contract_status_id' => ContractState::AngebotVersendet, 'cleaning_mail' => true]);
        $this->offerSentAt($event, '2025-12-20');

        $this->assertSame([], $this->titlesFor($event));
    }

    public function test_own_booking_without_cleaning_mail_asks_to_send_it(): void
    {
        $event = Event::factory()->create(['event_status_id' => BookingState::Eigene, 'start_date' => '2026-03-07', 'end_date' => '2026-03-08', 'cleaning_mail' => false]);

        $this->assertSame(['Putzmail senden'], $this->titlesFor($event));
    }

    public function test_urgent_tasks_come_first(): void
    {
        Event::factory()->create(['contract_status_id' => ContractState::AngebotErstellt, 'start_date' => '2026-03-03', 'end_date' => '2026-03-04']);
        Event::factory()->confirmed()->past()->create(['code' => '4711', 'cleaning_mail' => true]);

        $this->assertSame(['Rechnung fehlt', 'Angebot versenden'], array_column($this->tasks(), 'title'));
    }

    private function offerSentAt(Event $event, string $date): void
    {
        $event->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => EventOfferSendNotification::class,
            'data' => [],
            'created_at' => $date,
        ]);
    }
}
