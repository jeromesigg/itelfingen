<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use App\Notifications\AdminPushNotification;
use App\Services\AdminPush;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Minishlink\WebPush\MessageSentReport;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use Mockery;
use NotificationChannels\WebPush\Events\NotificationFailed;
use NotificationChannels\WebPush\ReportHandler;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;
use RuntimeException;
use Tests\TestCase;

/**
 * Push-Meldungen an Verwalter und Admins (AdminPush). Welche Formulare sie auslösen,
 * prüfen CreateMultiDayEventTest, ContactTest und ApplicationTest.
 */
class AdminPushTest extends TestCase
{
    private function withPush(User $user): User
    {
        $user->updatePushSubscription('https://push.example.com/'.$user->id, 'public-key', 'auth-token');

        return $user;
    }

    public function test_only_active_managers_with_push_enabled_receive_it(): void
    {
        Notification::fake();
        $verwalter = $this->withPush(User::factory()->verwalter()->create());
        $admin = $this->withPush(User::factory()->admin()->create());
        $team = $this->withPush(User::factory()->team()->create());
        $archived = $this->withPush(User::factory()->verwalter()->create(['is_active' => false]));
        $withoutPush = User::factory()->verwalter()->create();

        app(AdminPush::class)->eventCreated(Event::factory()->create());

        Notification::assertSentTo([$verwalter, $admin], AdminPushNotification::class);
        Notification::assertNotSentTo([$team, $archived, $withoutPush], AdminPushNotification::class);
    }

    public function test_booking_push_names_guest_and_dates_and_opens_booking(): void
    {
        Notification::fake();
        $verwalter = $this->withPush(User::factory()->verwalter()->create());
        $event = Event::factory()->create(['firstname' => 'Hans', 'name' => 'Muster', 'group_name' => null, 'start_date' => '2026-03-07', 'end_date' => '2026-03-09']);

        app(AdminPush::class)->eventCreated($event);

        Notification::assertSentTo($verwalter, AdminPushNotification::class, function (AdminPushNotification $notification) use ($verwalter, $event) {
            $message = $notification->toWebPush($verwalter, $notification)->toArray();

            return $message['title'] === 'Neue Buchungsanfrage'
                && $message['body'] === 'Hans Muster · 07.03.–09.03.2026'
                && $message['data'] === ['url' => route('admin.events.edit', $event->id)];
        });
    }

    public function test_push_waits_in_database_queue_until_the_scheduled_worker_runs(): void
    {
        config(['queue.default' => 'database']);
        $verwalter = $this->withPush(User::factory()->verwalter()->create());
        // Nur der Versand ins Internet ist nachgebaut, der Kanal des Pakets läuft echt
        $sent = [];
        $webPush = Mockery::mock(WebPush::class);
        $webPush->shouldReceive('queueNotification')->andReturnUsing(function (Subscription $subscription, string $payload) use (&$sent) {
            $sent[$subscription->getEndpoint()] = json_decode($payload, true)['title'];
        });
        $webPush->shouldReceive('flush')->andReturnUsing(fn () => yield new MessageSentReport(new Request('POST', 'https://push.example.com/'.$verwalter->id)));
        $this->app->instance(WebPushChannel::class, new WebPushChannel($webPush, app(ReportHandler::class)));

        app(AdminPush::class)->eventCreated(Event::factory()->create());
        $this->assertDatabaseCount('jobs', 1);

        $this->artisan('queue:work --stop-when-empty --max-time=50 --tries=3')->assertSuccessful();
        $this->assertDatabaseCount('jobs', 0);
        $this->assertSame(['https://push.example.com/'.$verwalter->id => 'Neue Buchungsanfrage'], $sent);
    }

    public function test_rejected_push_is_logged_with_reason(): void
    {
        Log::spy();
        $verwalter = $this->withPush(User::factory()->verwalter()->create());
        $subscription = $verwalter->pushSubscriptions()->first();
        $report = new MessageSentReport(new Request('POST', $subscription->endpoint), new Response(403), false, 'VAPID credentials mismatch');

        event(new NotificationFailed($report, $subscription, new WebPushMessage));

        Log::shouldHaveReceived('warning')->with('Push-Meldung abgelehnt', Mockery::on(fn (array $context) => $context['user_id'] === $verwalter->id
            && $context['push_service'] === 'push.example.com'
            && $context['status'] === 403
            && $context['reason'] === 'VAPID credentials mismatch'));
    }

    public function test_failing_push_is_reported_instead_of_thrown(): void
    {
        Exceptions::fake();
        $this->withPush(User::factory()->verwalter()->create());
        $this->mock(WebPushChannel::class)->shouldReceive('send')->andThrow(new RuntimeException('Push-Dienst nicht erreichbar'));

        app(AdminPush::class)->eventCreated(Event::factory()->create());

        Exceptions::assertReported(RuntimeException::class);
    }
}
