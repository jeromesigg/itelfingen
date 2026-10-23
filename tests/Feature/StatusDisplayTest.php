<?php

namespace Tests\Feature;

use App\Enums\BookingState;
use App\Enums\ContractState;
use App\Helper\Helper;
use App\Mail\MonthlyMail;
use App\Models\ContractStatus;
use App\Models\Event;
use App\Models\EventStatus;
use App\Models\User;
use App\View\Components\Bookings\Entry;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Support\BookingScenario;
use Tests\TestCase;

/**
 * Anzeigen, die vom Buchungs- und Vertragsstatus abhängen: Statusspalte der Buchungsliste (Helper),
 * erfüllte Schritte im Kundenbereich (Bookings\Entry), Markierungen in der Monatsmail.
 */
class StatusDisplayTest extends TestCase
{
    /**
     * [Buchungsstatus, Vertragsstatus, Offerte?, Rechnung?, erwartete Teile der Statusspalte, nicht erwartete Teile]
     */
    public static function statusColumn(): array
    {
        return [
            'neu, offen' => [5, 5, false, false, ['Kein Angebot erstellt', 'Keine Rechnung erstellt', 'Kein Putzmail versendet', 'Kein Code zugewiesen'], []],
            'Angebot erstellt' => [5, 7, true, false, ['Angebot</a> erstellt<br>'], ['versendet & akzeptiert']],
            'Angebot versendet' => [5, 10, true, false, ['Angebot</a> erstellt & versendet<br>'], []],
            'Rechnung erstellt' => [10, 20, true, true, ['Angebot</a> erstellt, versendet & akzeptiert', 'Rechnung</a> erstellt<br>'], []],
            'Rechnung versendet' => [10, 30, true, true, ['Rechnung</a> erstellt & versendet'], []],
            // Eigene Termine und Stornos: keine Offerte, Rechnung, Code; Stornos auch kein Putzmail
            'eigene Termine' => [20, 5, false, false, ['Kein Putzmail versendet'], ['Kein Angebot', 'Keine Rechnung', 'Kein Code']],
            'storniert' => [50, 50, true, true, [], ['Angebot', 'Rechnung', 'Putzmail', 'Code']],
        ];
    }

    #[DataProvider('statusColumn')]
    public function test_status_column(int $booking, int $contract, bool $offer, bool $invoice, array $contains, array $missing): void
    {
        $event = Event::factory()->create([
            'event_status_id' => $booking,
            'contract_status_id' => $contract,
            'bexio_offer_id' => $offer ? 11 : null,
            'bexio_invoice_id' => $invoice ? 22 : null,
        ])->fresh();

        $status = $event->status();

        foreach ($contains as $text) {
            $this->assertStringContainsString($text, $status);
        }
        foreach ($missing as $text) {
            $this->assertStringNotContainsString($text, $status);
        }
    }

    /**
     * [Buchungsstatus, Vertragsstatus, Offerte?, Rechnung?, Putzmail?, Code, erwartete Prüfpunkte]
     */
    public static function mobileChecks(): array
    {
        return [
            'neu, nichts erledigt' => [5, 5, false, false, false, null, ['Angebot' => false, 'Rechnung' => false, 'Putzmail' => false, 'Code' => false]],
            'bestätigt, alles erledigt' => [10, 20, true, true, true, '4711', ['Angebot' => true, 'Rechnung' => true, 'Putzmail' => true, 'Code' => true]],
            'eigene Termine: nur Putzmail' => [20, 5, false, false, true, null, ['Putzmail' => true]],
            'storniert: keine Prüfpunkte' => [50, 50, true, true, true, '4711', []],
        ];
    }

    #[DataProvider('mobileChecks')]
    public function test_booking_table_sends_checks_and_state_for_mobile_cards(int $booking, int $contract, bool $offer, bool $invoice, bool $cleaningMail, ?string $code, array $expected): void
    {
        $verwalter = User::factory()->verwalter()->create();
        Event::factory()->create([
            'event_status_id' => $booking,
            'contract_status_id' => $contract,
            'bexio_offer_id' => $offer ? 11 : null,
            'bexio_invoice_id' => $invoice ? 22 : null,
            'cleaning_mail' => $cleaningMail,
            'code' => $code,
        ]);

        // Nach dem Vertragsstatus filtern: "Alle" blendet stornierte Verträge aus
        $status = ContractStatus::find($contract)->name;
        $row = $this->actingAs($verwalter)->getJson('/events/datatable?date=Alle&status='.urlencode($status))->assertOk()->json('data.0');

        $this->assertSame($expected, array_column($row['checks'], 'done', 'label'));
        $this->assertSame(EventStatus::find($booking)->name, $row['state']);
    }

    public static function userColumn(): array
    {
        return [
            'eigene Termine' => [20, 'Interne Buchung'],
            'storniert' => [50, 'Storniert'],
            'neu ohne Verwalter' => [5, 'Kein Benutzer zugewiesen'],
        ];
    }

    #[DataProvider('userColumn')]
    public function test_user_column(int $booking, string $text): void
    {
        $event = Event::factory()->create(['event_status_id' => $booking])->fresh();

        $this->assertStringContainsString($text, Helper::getEventUserCheck($event));
    }

    public static function customerSteps(): array
    {
        // Vertragsstatus → erfüllte Schritte (Created, OfferSend, InvoiceCreated, Feedback)
        return [
            'offen' => [5, [true, false, false, false]],
            'Angebot versendet' => [10, [true, true, false, false]],
            'Rechnung erstellt' => [20, [true, true, true, false]],
            'Rechnung versendet' => [30, [true, true, true, true]],
        ];
    }

    #[DataProvider('customerSteps')]
    public function test_customer_area_steps(int $contract, array $fulfilled): void
    {
        $event = Event::factory()->create(['contract_status_id' => $contract])->fresh();

        $actual = array_map(fn ($type) => (bool) (new Entry($type, $event))->fulfilled, ['Created', 'OfferSend', 'InvoiceCreated', 'Feedback']);

        $this->assertSame($fulfilled, $actual);
    }

    public function test_empty_states_count_as_lowest_like_before(): void
    {
        // Spalten sind nullable; der alte Code verglich null < x, also wie der kleinste Status.
        $event = Event::factory()->create(['event_status_id' => null, 'contract_status_id' => null])->fresh();

        $this->assertSame(BookingState::Neu, $event->bookingState());
        $this->assertSame(ContractState::Offen, $event->contractState());
        $this->assertTrue((bool) (new Entry('Created', $event))->fulfilled);
        $this->assertFalse((bool) (new Entry('OfferSend', $event))->fulfilled);
    }

    public function test_monthly_mail_marks_provisional_and_cancelled(): void
    {
        $new = Event::factory()->create(['name' => 'Neu']);
        $confirmed = Event::factory()->confirmed()->create(['name' => 'Fest']);
        $cancelled = Event::factory()->cancelled()->create(['name' => 'Abgesagt']);

        $html = (new MonthlyMail(Event::whereKey([$new->id, $confirmed->id, $cancelled->id])->get()))->render();

        $this->assertSame(1, substr_count($html, 'provisorisch'));
        $this->assertSame(1, substr_count($html, 'storniert'));
    }

    public function test_events_page_shows_status_names_via_relation(): void
    {
        $s = BookingScenario::create();

        $this->actingAs($s->user('verwalter'))->get("/admin/events/{$s->confirmedEvent->id}/edit")
            ->assertOk()
            ->assertSee('Rechnung erstellt');
    }
}
