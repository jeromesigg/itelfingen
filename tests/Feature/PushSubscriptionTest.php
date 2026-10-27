<?php

namespace Tests\Feature;

use App\Models\User;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Minishlink\WebPush\MessageSentReport;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use Mockery;
use NotificationChannels\WebPush\ReportHandler;
use NotificationChannels\WebPush\WebPushChannel;
use Tests\TestCase;

/**
 * Push-Meldungen auf dem eigenen Gerät ein- und ausschalten (Profil, PushSubscriptionController).
 * Dass Team-Mitglieder die Routen nicht aufrufen dürfen, prüft Security/RoleAccessTest.
 */
class PushSubscriptionTest extends TestCase
{
    private const ENDPOINT = 'https://fcm.googleapis.com/fcm/send/abc123';

    /** @return array<string, mixed> */
    private function subscription(array $overrides = []): array
    {
        return array_merge([
            'endpoint' => self::ENDPOINT,
            'expirationTime' => null,
            'keys' => ['p256dh' => 'BNcRdreALRFXTkOOUHK1EtK2wtaz5Ry4YfYCA_0QTpQtUbVlUls0VJXg7A8u-Ts1XbjhazAkj7I99e8QcYP7DkM', 'auth' => 'tBHItJI5svbpez7KI4CCXg'],
            'contentEncoding' => 'aes128gcm',
        ], $overrides);
    }

    public function test_manager_enables_push_on_device(): void
    {
        $verwalter = User::factory()->verwalter()->create();

        $response = $this->actingAs($verwalter)->postJson('/admin/push-subscriptions', $this->subscription());

        $response->assertCreated()->assertJson(['subscribed' => true]);
        $this->assertDatabaseHas('push_subscriptions', [
            'subscribable_id' => $verwalter->id,
            'endpoint' => self::ENDPOINT,
            'auth_token' => 'tBHItJI5svbpez7KI4CCXg',
            'content_encoding' => 'aes128gcm',
        ]);
    }

    public function test_endpoint_must_be_https(): void
    {
        $verwalter = User::factory()->verwalter()->create();

        $response = $this->actingAs($verwalter)->postJson('/admin/push-subscriptions', $this->subscription(['endpoint' => 'http://intern.local/push']));

        $response->assertUnprocessable()->assertJsonValidationErrors(['endpoint' => 'endpoint muss einen der folgenden Anfänge aufweisen: https://']);
        $this->assertDatabaseCount('push_subscriptions', 0);
    }

    public function test_disabling_push_removes_only_own_device(): void
    {
        $verwalter = User::factory()->verwalter()->create();
        $other = User::factory()->admin()->create();
        $verwalter->updatePushSubscription(self::ENDPOINT, 'key', 'token');
        $other->updatePushSubscription('https://fcm.googleapis.com/fcm/send/other', 'key', 'token');

        $this->actingAs($verwalter)->deleteJson('/admin/push-subscriptions', ['endpoint' => self::ENDPOINT])->assertOk();
        $this->actingAs($verwalter)->deleteJson('/admin/push-subscriptions', ['endpoint' => 'https://fcm.googleapis.com/fcm/send/other'])->assertOk();

        $this->assertDatabaseMissing('push_subscriptions', ['endpoint' => self::ENDPOINT]);
        $this->assertDatabaseHas('push_subscriptions', ['subscribable_id' => $other->id]);
    }

    /**
     * Antwort des Push-Dienstes für die Testmeldung. Nur der Versand ins Internet (WebPush) ist nachgebaut,
     * der Kanal des Pakets läuft echt; merkt sich, an welche Geräte gesendet wurde.
     *
     * @param  list<string>  $sentTo
     */
    private function pushServiceAnswers(MessageSentReport $report, array &$sentTo = []): void
    {
        $webPush = Mockery::mock(WebPush::class);
        $webPush->shouldReceive('queueNotification')->andReturnUsing(function (Subscription $subscription) use (&$sentTo) {
            $sentTo[] = $subscription->getEndpoint();
        });
        $webPush->shouldReceive('flush')->andReturnUsing(fn () => yield $report);
        $this->app->instance(WebPushChannel::class, new WebPushChannel($webPush, app(ReportHandler::class)));
    }

    public function test_test_message_goes_only_to_this_device(): void
    {
        $verwalter = User::factory()->verwalter()->create();
        $verwalter->updatePushSubscription(self::ENDPOINT, 'key', 'token');
        $verwalter->updatePushSubscription('https://fcm.googleapis.com/fcm/send/laptop', 'key', 'token');
        $sentTo = [];
        $this->pushServiceAnswers(new MessageSentReport(new Request('POST', self::ENDPOINT)), $sentTo);

        $response = $this->actingAs($verwalter)->postJson('/admin/push-subscriptions/test', ['endpoint' => self::ENDPOINT]);

        $response->assertOk()->assertJson(['message' => 'Vom Push-Dienst angenommen. Erscheint keine Meldung, sind Mitteilungen auf dem Gerät ausgeschaltet.']);
        $this->assertSame([self::ENDPOINT], $sentTo);
    }

    public function test_test_message_shows_why_push_service_rejected_it(): void
    {
        $verwalter = User::factory()->verwalter()->create();
        $verwalter->updatePushSubscription(self::ENDPOINT, 'key', 'token');
        $this->pushServiceAnswers(new MessageSentReport(new Request('POST', self::ENDPOINT), new Response(403), false, 'VAPID credentials mismatch'));

        $response = $this->actingAs($verwalter)->postJson('/admin/push-subscriptions/test', ['endpoint' => self::ENDPOINT]);

        $response->assertStatus(502)->assertJson(['message' => 'Der Push-Dienst hat abgelehnt (403): VAPID credentials mismatch']);
    }

    public function test_test_message_for_device_unknown_to_server_asks_to_turn_push_on_again(): void
    {
        $verwalter = User::factory()->verwalter()->create();
        $other = User::factory()->admin()->create();
        $other->updatePushSubscription(self::ENDPOINT, 'key', 'token');
        $this->mock(WebPushChannel::class)->shouldNotReceive('send');

        $response = $this->actingAs($verwalter)->postJson('/admin/push-subscriptions/test', ['endpoint' => self::ENDPOINT]);

        $response->assertUnprocessable()->assertJson(['message' => 'Dieses Gerät ist auf dem Server nicht angemeldet. Bitte «Ausschalten» und wieder «Einschalten».']);
    }

    public function test_profile_explains_missing_server_keys_instead_of_offering_push(): void
    {
        config(['webpush.vapid.public_key' => null]);
        $verwalter = User::factory()->verwalter()->create();

        $response = $this->actingAs($verwalter)->get("/admin/users/{$verwalter->id}/edit");

        $response->assertOk()
            ->assertSee('VAPID-Schlüssel fehlen')
            ->assertDontSee('push-toggle-config');
    }

    public function test_push_settings_appear_only_in_own_profile_of_managers(): void
    {
        $verwalter = User::factory()->verwalter()->create();
        $team = User::factory()->team()->create();
        $admin = User::factory()->admin()->create();

        $this->actingAs($verwalter)->get("/admin/users/{$verwalter->id}/edit")->assertOk()->assertSee('Push-Meldungen');
        $this->actingAs($team)->get("/admin/users/{$team->id}/edit")->assertOk()->assertDontSee('Push-Meldungen');
        $this->actingAs($admin)->get("/admin/users/{$verwalter->id}/edit")->assertOk()->assertDontSee('Push-Meldungen');
    }
}
