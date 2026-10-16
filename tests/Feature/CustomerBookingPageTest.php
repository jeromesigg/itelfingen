<?php

namespace Tests\Feature;

use App\Enums\ContractState;
use App\Models\BexioToken;
use Illuminate\Support\Facades\Http;
use Tests\Support\BookingScenario;
use Tests\TestCase;

/**
 * Kundenbereich: Übersicht der eigenen Buchung (bookings/{uuid}) mit Links auf Offerte und Rechnung in Bexio.
 */
class CustomerBookingPageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        BexioToken::create(['access_token' => 'token', 'refresh_token' => 'refresh', 'expires_at' => now()->addHour()]);
    }

    public function test_new_booking_page_renders_without_calling_bexio(): void
    {
        Http::fake();
        $s = BookingScenario::create();

        // Ohne Offerte und Rechnung ging der Aufruf an "kb_offer/" (Liste aller Offerten).
        $this->get("/bookings/{$s->event->uuid}")
            ->assertOk()
            ->assertSee('Buchung erstellt');

        Http::assertNothingSent();
    }

    public function test_booking_page_links_offer_and_invoice_from_bexio(): void
    {
        Http::fake([
            'api.bexio.com/2.0/kb_offer/11' => Http::response(['id' => 11, 'network_link' => 'https://bexio.test/offerte']),
            'api.bexio.com/2.0/kb_invoice/22' => Http::response(['id' => 22, 'network_link' => 'https://bexio.test/rechnung']),
        ]);
        $s = BookingScenario::create();
        $s->confirmedEvent->update([
            'bexio_offer_id' => 11,
            'bexio_invoice_id' => 22,
            'contract_status_id' => ContractState::RechnungVersendet,
        ]);

        $this->get("/bookings/{$s->confirmedEvent->uuid}")
            ->assertOk()
            ->assertSee('https://bexio.test/offerte')
            ->assertSee('https://bexio.test/rechnung');
    }

    public function test_booking_page_renders_when_bexio_is_unavailable(): void
    {
        Http::fake(['api.bexio.com/*' => Http::response('Service Unavailable', 503)]);
        $s = BookingScenario::create();
        $s->confirmedEvent->update(['bexio_offer_id' => 11, 'bexio_invoice_id' => 22]);

        $this->get("/bookings/{$s->confirmedEvent->uuid}")
            ->assertOk()
            ->assertSee('Angebot versendet')
            ->assertDontSee('bexio.test');
    }

    public function test_booking_page_renders_without_bexio_connection(): void
    {
        Http::fake();
        BexioToken::query()->delete();
        $s = BookingScenario::create();
        $s->confirmedEvent->update(['bexio_offer_id' => 11]);

        $this->get("/bookings/{$s->confirmedEvent->uuid}")->assertOk();
    }

    public function test_status_banner_shows_current_stage(): void
    {
        Http::fake();
        $s = BookingScenario::create();

        $this->get("/bookings/{$s->event->uuid}")->assertSee('Buchung eingegangen');
        $this->get("/bookings/{$s->confirmedEvent->uuid}")->assertSee('Deine Buchung ist definitiv');
        $this->get("/bookings/{$s->cancelledEvent->uuid}")->assertSee('Buchung storniert');
        $this->get("/bookings/{$s->pastEvent->uuid}")->assertSee('Danke für deinen Aufenthalt');
    }

    public function test_offer_stage_links_offer_in_status_banner(): void
    {
        Http::fake([
            'api.bexio.com/2.0/kb_offer/11' => Http::response(['id' => 11, 'network_link' => 'https://bexio.test/offerte']),
        ]);
        $s = BookingScenario::create();
        $s->event->update(['bexio_offer_id' => 11, 'contract_status_id' => ContractState::AngebotVersendet]);

        $this->get("/bookings/{$s->event->uuid}")
            ->assertOk()
            ->assertSee('Angebot versendet – bitte prüfen')
            ->assertSee('https://bexio.test/offerte');

        Http::assertSentCount(1);
    }

    public function test_codes_are_shown_only_after_last_infos(): void
    {
        Http::fake();
        $s = BookingScenario::create();
        $s->confirmedEvent->update(['code' => '987654']);

        $this->get("/bookings/{$s->confirmedEvent->uuid}")
            ->assertDontSee('Deine Codes')
            ->assertDontSee('987654');

        $s->confirmedEvent->update(['last_info' => true]);

        $this->get("/bookings/{$s->confirmedEvent->uuid}")
            ->assertSee('Alles bereit für deine Anreise')
            ->assertSee('Deine Codes')
            ->assertSee('987654')
            ->assertDontSee('Abfallcontainer');
    }

    public function test_waste_code_is_shown_from_start_date(): void
    {
        Http::fake();
        $s = BookingScenario::create();
        $s->confirmedEvent->update([
            'last_info' => true,
            'start_date' => today()->format('Y-m-d'),
            'end_date' => today()->addDays(2)->format('Y-m-d'),
        ]);

        $this->get("/bookings/{$s->confirmedEvent->uuid}")
            ->assertSee('Abfallcontainer')
            ->assertSee('4315');
    }
}
