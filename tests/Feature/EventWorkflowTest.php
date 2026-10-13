<?php

namespace Tests\Feature;

use App\Models\BexioToken;
use App\Models\Event;
use App\Models\Newsletter;
use App\Models\Position;
use App\Models\PricelistPosition;
use App\Notifications\EventApplicationWantedNotification;
use App\Notifications\EventCleaningSentNotification;
use App\Notifications\EventInvoiceCreatedNotification;
use App\Notifications\EventInvoiceSendNotification;
use App\Notifications\EventOfferSendNotification;
use App\Services\GoogleCalendarService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Tests\Support\BookingScenario;
use Tests\TestCase;

/**
 * Kernablauf einer Buchung im Admin (Formular "Buchung bearbeiten", Buttons 1-6):
 * Offen → Angebot erstellt → Angebot versendet → Rechnung erstellt → Rechnung versendet.
 * Bexio ist gefakt; Anfragen werden mit Http::assertSent() geprüft.
 */
class EventWorkflowTest extends TestCase
{
    private BookingScenario $s;

    private bool $bexioFaked = false;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
        BexioToken::create(['access_token' => 'token', 'refresh_token' => 'refresh', 'expires_at' => now()->addHour()]);
        $this->s = BookingScenario::create();
    }

    /**
     * Bexio-Antworten; $contacts ist das Ergebnis der Kontaktsuche. Pro Test einmal aufrufen
     * (ein zweites Http::fake() hängt seine Regeln nur hinten an).
     */
    private function fakeBexio(array $contacts = []): void
    {
        Http::fake([
            'api.bexio.com/2.0/contact/search' => Http::response($contacts),
            'api.bexio.com/2.0/contact' => Http::response(['id' => 77]),
            'api.bexio.com/2.0/kb_offer' => Http::response(['id' => 11]),
            'api.bexio.com/2.0/kb_offer/11/invoice' => Http::response(['id' => 22]),
            'api.bexio.com/*' => Http::response(['id' => 1]),
        ]);
    }

    /**
     * Formular "Buchung bearbeiten" wie es der Browser schickt, mit dem Wert des gedrückten Buttons.
     */
    private function submit(Event $event, string $button, array $overrides = [])
    {
        if (! $this->bexioFaked) {
            $this->fakeBexio();
        }

        $data = array_merge($event->only([
            'name', 'firstname', 'group_name', 'email', 'street', 'house_number', 'plz', 'city', 'telephone',
            'event_status_id', 'contract_status_id', 'start_date', 'end_date', 'total_days', 'total_people', 'comment',
        ]), ['submit' => $button], $overrides);

        return $this->actingAs($this->s->user('verwalter'))
            ->from("/admin/events/{$event->id}/edit")
            ->patch("/admin/events/{$event->id}", $data);
    }

    private function bexioRequests(): array
    {
        return Http::recorded()->map(fn ($pair) => $pair[0]->method().' '.str_replace('https://api.bexio.com/2.0/', '', $pair[0]->url()))->all();
    }

    public function test_update_button_saves_booking_without_bexio(): void
    {
        $event = $this->s->event;
        $position = $event->positions()->first();

        $this->submit($event, '1 Buchung updaten', [
            'name' => 'Neu',
            'telephone' => '079 111 22 33',
            'positions' => [$position->id => 9],
        ])->assertRedirect("/admin/events/{$event->id}/edit");

        $event->refresh();
        $this->assertSame('Neu', $event->name);
        $this->assertSame('0791112233', $event->phonenumber_query);
        $this->assertSame(9, $position->fresh()->amount);
        Http::assertNothingSent();
        Notification::assertNothingSent();
    }

    public function test_update_only_changes_positions_of_this_booking(): void
    {
        $foreign = $this->s->confirmedEvent->positions()->first();
        $amount = $foreign->amount;

        $this->submit($this->s->event, '1 Buchung updaten', ['positions' => [$foreign->id => 99]]);

        $this->assertSame($amount, $foreign->fresh()->amount);
    }

    public function test_cancelling_the_contract_cancels_the_booking(): void
    {
        $this->submit($this->s->event, '1 Buchung updaten', ['contract_status_id' => config('status.contract_storniert')]);

        $this->assertSame(config('status.event_storniert'), $this->s->event->fresh()->event_status_id);
    }

    public function test_create_offer_creates_contact_and_offer_in_bexio(): void
    {
        $event = $this->s->event;
        $event->update(['total_days' => 2, 'discount' => 10]);
        // 5 Parkplätze (3 inbegriffen) und eine Endreinigung (fix)
        $event->positions()->whereRelation('pricelist_position', 'bexio_code', 210)->update(['amount' => 5]);
        Position::create(['event_id' => $event->id, 'pricelist_position_id' => PricelistPosition::where('bexio_code', 220)->value('id'), 'amount' => 1]);

        $this->submit($event, '2 Angebot erstellen');

        $event->refresh();
        $this->assertSame(77, $event->bexio_user_id);
        $this->assertSame(11, $event->bexio_offer_id);
        $this->assertSame(config('status.contract_angebot_erstellt'), $event->contract_status_id);

        Http::assertSent(fn (Request $r) => $r->url() === 'https://api.bexio.com/2.0/contact' && $r['name_1'] === $event->name);
        Http::assertSent(function (Request $r) {
            if ($r->url() !== 'https://api.bexio.com/2.0/kb_offer') {
                return false;
            }
            $positions = collect($r['positions'])->keyBy('article_id');

            // Regeln siehe tests/README.md (Haus 1, Erwachsene 6, Kinder 4, Parkplätze 5, Endreinigung 1; 2 Nächte)
            return $r['contact_id'] === 77
                && $positions[1020]['amount'] == 1          // Hausmiete: Menge wie erfasst
                && $positions[1030]['amount'] === 12        // Personen: Menge × Nächte (als Ganzzahl an Bexio)
                && $positions[1040]['amount'] == 8
                && $positions[1210]['amount'] == 4          // Parkplätze: (5 − 3 inbegriffen) × Nächte
                && $positions[1220]['amount'] == 1          // übrige Zusatzkosten: fix
                && $positions[1030]['discount_in_percent'] == 0
                && $positions[1220]['discount_in_percent'] == 10;
        });
    }

    public function test_offer_omits_parking_within_included_spots(): void
    {
        $event = $this->s->event;
        $event->positions()->whereRelation('pricelist_position', 'bexio_code', 210)->update(['amount' => 3]);

        $this->submit($event, '2 Angebot erstellen');

        Http::assertSent(fn (Request $r) => $r->url() === 'https://api.bexio.com/2.0/kb_offer'
            && ! collect($r['positions'])->pluck('article_id')->contains(1210));
    }

    public function test_create_offer_reuses_existing_bexio_contact(): void
    {
        $this->fakeBexio([['id' => 55]]);
        $this->bexioFaked = true;

        $this->submit($this->s->event, '2 Angebot erstellen');

        $this->assertSame(55, $this->s->event->fresh()->bexio_user_id);
        Http::assertNotSent(fn (Request $r) => $r->url() === 'https://api.bexio.com/2.0/contact');
    }

    public function test_send_offer_button_with_leading_space_sends_offer(): void
    {
        // Der Button in der View hat den Wert " 3 Angebot versenden" (mit Leerzeichen); TrimStrings rettet ihn.
        $event = $this->s->event;
        $event->update(['bexio_user_id' => 77, 'bexio_offer_id' => 11, 'contract_status_id' => config('status.contract_angebot_erstellt')]);

        $this->submit($event->fresh(), ' 3 Angebot versenden', ['additional_text' => 'Bis bald']);

        $this->assertSame(config('status.contract_angebot_versendet'), $event->fresh()->contract_status_id);
        // GET: die Benachrichtigung holt den Link der Offerte für die Mail
        $this->assertSame(['POST kb_offer/11/issue', 'POST kb_offer/11/send', 'GET kb_offer/11'], $this->bexioRequests());
        Notification::assertSentTo($event, EventOfferSendNotification::class);
    }

    public function test_send_offer_without_offer_does_nothing(): void
    {
        $this->submit($this->s->event, '3 Erinnerung versenden');

        Http::assertNothingSent();
        Notification::assertNothingSent();
    }

    public function test_create_invoice_confirms_booking(): void
    {
        $event = $this->s->event;
        $event->update(['bexio_user_id' => 77, 'bexio_offer_id' => 11, 'contract_status_id' => config('status.contract_angebot_versendet')]);

        $this->submit($event->fresh(), '4 Rechnung erstellen');

        $event->refresh();
        $this->assertSame(22, $event->bexio_invoice_id);
        $this->assertSame(config('status.event_bestaetigt'), $event->event_status_id);
        $this->assertSame(config('status.contract_rechnung_erstellt'), $event->contract_status_id);
        $this->assertContains('POST kb_offer/11/invoice', $this->bexioRequests());
        Http::assertSent(fn (Request $r) => $r->url() === 'https://api.bexio.com/2.0/kb_invoice/22'
            && $r['is_valid_to'] === now()->parse($event->end_date)->addDays(30)->toDateString());
        Notification::assertSentTo($event, EventInvoiceCreatedNotification::class);

        // Kalender, Checkliste (alle aktiven Räume mit ihren Checkpunkten), Newsletter
        $this->assertTrue($event->is(app(GoogleCalendarService::class)->bookings[0]));
        $this->assertSame(1, $event->event_rooms()->count());
        $this->assertSame(1, $event->event_rooms()->first()->event_checkpoints()->count());
        $this->assertTrue(Newsletter::where('email', $event->email)->sole()->bookings);
    }

    public function test_create_invoice_twice_does_nothing(): void
    {
        $event = $this->s->confirmedEvent;
        $event->update(['bexio_offer_id' => 11, 'bexio_invoice_id' => 22]);

        $this->submit($event->fresh(), '4 Rechnung erstellen');

        Http::assertNothingSent();
        Notification::assertNothingSent();
    }

    public function test_send_invoice(): void
    {
        $event = $this->s->confirmedEvent;
        $event->update(['bexio_offer_id' => 11, 'bexio_invoice_id' => 22]);

        $this->submit($event->fresh(), '5 Rechnung versenden');

        $this->assertSame(config('status.contract_rechnung_versendet'), $event->fresh()->contract_status_id);
        $this->assertSame(['POST kb_invoice/22', 'POST kb_invoice/22/issue', 'POST kb_invoice/22/send', 'GET kb_invoice/22'], $this->bexioRequests());
        Notification::assertSentTo($event, EventInvoiceSendNotification::class);
    }

    public function test_send_cooperative_info(): void
    {
        $event = $this->s->confirmedEvent;

        $this->submit($event, '6 Genossenschafts Info versenden');

        Notification::assertSentTo($event, EventApplicationWantedNotification::class);
        Http::assertNothingSent();
    }

    public function test_cleaning_mail_is_sent_and_marked(): void
    {
        $event = $this->s->confirmedEvent;

        $this->actingAs($this->s->user('verwalter'))->from("/admin/events/{$event->id}/edit")
            ->post("/admin/events/{$event->id}/SendCleaningMail", [
                'cleaning_mail_address' => 'putz@example.com',
                'cleaning_mail_text' => 'Bitte am Sonntag reinigen',
            ])->assertRedirect("/admin/events/{$event->id}/edit");

        $this->assertTrue($event->fresh()->cleaning_mail);
        Notification::assertSentTo($event, EventCleaningSentNotification::class);
    }

    public function test_every_button_in_the_view_is_handled_by_the_controller(): void
    {
        // In jedem Vertragsstatus die Buttons der View lesen und prüfen, dass der Controller ihren Wert kennt.
        $event = $this->s->event;
        $handled = ['1', '2', '3', '4', '5', '6'];
        $seen = [];

        foreach ([5, 7, 10, 20, 30] as $status) {
            $event->update(['contract_status_id' => $status, 'bexio_offer_id' => 11, 'bexio_invoice_id' => 22]);
            $html = $this->actingAs($this->s->user('verwalter'))->get("/admin/events/{$event->id}/edit")->assertOk()->getContent();
            preg_match_all('/name="submit" value="([^"]*)"/', $html, $buttons);
            foreach ($buttons[1] as $value) {
                $seen[] = $value;
                $this->assertContains(substr(trim($value), 0, 1), $handled, "Button \"$value\" (Status $status) wird im Controller nicht behandelt");
            }
        }

        // 1 updaten, 2 Angebot erstellen, 3 Angebot versenden, 3 Erinnerung, 4 Rechnung erstellen, 5 versenden, 6 Info
        $this->assertCount(7, array_unique(array_map('trim', $seen)));
    }
}
