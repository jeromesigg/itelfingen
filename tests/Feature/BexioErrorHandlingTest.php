<?php

namespace Tests\Feature;

use App\Enums\ContractState;
use App\Exceptions\BexioException;
use App\Models\BexioToken;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Fehler von Bexio im Admin-Bereich: Meldung als Toast statt Fehlerseite, der Fehler wird trotzdem geloggt.
 */
class BexioErrorHandlingTest extends TestCase
{
    /** @return array<string, array{0: \Closure, 1: string}> */
    public static function bexioFailures(): array
    {
        return [
            'Fehlercode im JSON' => [fn () => Http::response(['error_code' => 400, 'message' => 'Artikel unbekannt']), 'Bexio API Fehler 400: Artikel unbekannt'],
            'Fehlerantwort' => [fn () => Http::response(['message' => 'Server kaputt'], 500), 'Bexio antwortet mit Fehler 500: Server kaputt'],
            'keine Verbindung' => [fn () => Http::failedConnection(), 'Bexio ist nicht erreichbar.'],
        ];
    }

    #[DataProvider('bexioFailures')]
    public function test_failed_offer_returns_to_booking_with_message(\Closure $bexioResponse, string $message): void
    {
        Notification::fake();
        Exceptions::fake();
        BexioToken::create(['access_token' => 'token', 'refresh_token' => 'refresh', 'expires_at' => now()->addHour()]);
        $event = Event::factory()->create(['comment' => 'alt']);
        Http::fake(['api.bexio.com/*' => $bexioResponse()]);
        $edit = "/admin/events/{$event->id}/edit";

        $response = $this->actingAs(User::factory()->verwalter()->create())->from($edit)->patch("/admin/events/{$event->id}", [
            ...$event->only(['name', 'firstname', 'email', 'telephone', 'start_date', 'end_date']),
            'comment' => 'neu',
            'submit' => '2 Angebot erstellen',
        ]);

        $response->assertRedirect($edit)->assertSessionHas('error', 'Die Aktion bei Bexio ist fehlgeschlagen. '.$message);
        $this->get($edit)->assertOk()->assertSee('Die Aktion bei Bexio ist fehlgeschlagen. '.$message);
        Exceptions::assertReported(BexioException::class);
        // Gespeichert ist, was vor dem Bexio-Aufruf passierte; ein Angebot gibt es nicht
        $this->assertSame('neu', $event->fresh()->comment);
        $this->assertSame(ContractState::Offen, $event->fresh()->contract_status_id);
    }

    public function test_failing_page_load_returns_to_dashboard_instead_of_reloading_the_page(): void
    {
        Exceptions::fake();
        // Ohne Token wirft schon die Anmeldung bei Bexio
        $page = '/admin/positions/create';

        $response = $this->actingAs(User::factory()->admin()->create())->from($page)->get($page);

        $response->assertRedirect('/admin')->assertSessionHas('error');
        Exceptions::assertReported(BexioException::class);
    }

    public function test_public_pages_keep_the_error_page(): void
    {
        Exceptions::fake();
        Route::middleware('web')->get('/bexio-oeffentlich', fn () => throw new BexioException('kaputt'));

        $response = $this->get('/bexio-oeffentlich');

        $response->assertInternalServerError();
    }

    public function test_json_requests_in_admin_keep_the_error_status(): void
    {
        Exceptions::fake();
        Route::middleware('web')->post('/admin/bexio-json', fn () => throw new BexioException('kaputt'));

        $response = $this->actingAs(User::factory()->admin()->create())->postJson('/admin/bexio-json');

        $response->assertInternalServerError();
    }
}
