<?php

namespace Tests\Feature;

use App\Enums\BookingState;
use App\Enums\ContractState;
use App\Models\Event;
use App\Notifications\EventCreatedNotification;
use Illuminate\Support\Facades\Notification;
use Tests\Support\BookingScenario;
use Tests\TestCase;

/**
 * Öffentliches Buchungsformular auf der Startseite (POST event/create).
 */
class CreateMultiDayEventTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
        // Der Spam-Schutz wird in einem eigenen Test geprüft.
        config(['honeypot.enabled' => false]);
    }

    private function bookingData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Muster',
            'firstname' => 'Max',
            'group' => 'Cevi Zürich',
            'street' => 'Dorfstrasse',
            'house_number' => '1',
            'zipcode' => 8000,
            'city' => 'Zürich',
            'email' => 'max@example.com',
            'telephone' => '079 123-45 67',
            'terms' => '', // so schickt die Checkbox des Formulars den Haken
            '_form_scope' => 'event', // verstecktes Feld des Formulars, siehe OldInput
            'start_date' => now()->addMonth()->toDateString(),
            'end_date' => now()->addMonth()->addDays(2)->toDateString(),
            'total_days' => 2,
            'total_people' => 10,
            'total_amount' => 900,
            'discount' => 0,
            'positions' => [30 => 6, 40 => 4],
        ], $overrides);
    }

    public function test_multi_day_booking_creates_event_with_positions(): void
    {
        $this->from(route('home'))->post('/event/create', $this->bookingData())
            ->assertRedirect(route('home').'#booking')
            ->assertSessionHas('success_event');

        $event = Event::sole();
        $this->assertSame('Cevi Zürich', $event->group_name);
        $this->assertSame(8000, $event->plz);
        $this->assertSame('0791234567', $event->phonenumber_query);
        $this->assertSame(BookingState::Neu, $event->event_status_id);
        $this->assertSame(ContractState::Offen, $event->contract_status_id);
        $this->assertNotNull($event->uuid);

        // Hausmiete 1x, Personen wie erfasst, Reinigung mit 0
        $amounts = $event->positions()->with('pricelist_position')->get()
            ->mapWithKeys(fn ($p) => [$p->pricelist_position->bexio_code => $p->amount])
            ->sortKeys()->all();
        $this->assertSame([20 => 1, 30 => 6, 40 => 4, 210 => 0], $amounts);

        Notification::assertSentTo($event, EventCreatedNotification::class);
    }

    public function test_marketing_comment_is_stored_and_shown_to_manager(): void
    {
        $this->from(route('home'))->post('/event/create', $this->bookingData([
            'marketing_comment' => 'Empfehlung von Freunden',
        ]));

        $event = Event::sole();
        $this->assertSame('Empfehlung von Freunden', $event->marketing_comment);

        $s = BookingScenario::create();
        $this->actingAs($s->user('verwalter'))->get("/admin/events/{$event->id}/edit")
            ->assertOk()
            ->assertSee('Empfehlung von Freunden');
    }

    public function test_empty_position_fields_count_as_zero(): void
    {
        // Leere Felder kommen als null an (ConvertEmptyStringsToNull); amount ist NOT NULL.
        $this->from(route('home'))->post('/event/create', $this->bookingData([
            'positions' => [30 => '6', 40 => ''],
        ]))->assertSessionHasNoErrors();

        $amounts = Event::sole()->positions()->with('pricelist_position')->get()
            ->mapWithKeys(fn ($p) => [$p->pricelist_position->bexio_code => $p->amount])->all();
        $this->assertSame(6, $amounts[30]);
        $this->assertSame(0, $amounts[40]);
    }

    public function test_invalid_position_amounts_are_rejected(): void
    {
        $this->from(route('home'))->post('/event/create', $this->bookingData(['positions' => [30 => -2]]))
            ->assertSessionHasErrors('positions.30', null, 'event');
        $this->from(route('home'))->post('/event/create', $this->bookingData(['positions' => [30 => 'viele']]))
            ->assertSessionHasErrors('positions.30', null, 'event');

        $this->assertSame(0, Event::count());
    }

    public function test_booking_form_cannot_set_internal_fields(): void
    {
        $this->from(route('home'))->post('/event/create', $this->bookingData([
            'code' => 1234,
            'comment_intern' => 'von aussen',
            'external' => 1,
            'user_id' => 1,
            'bexio_invoice_id' => 99,
            'glutz_user_id' => 'fremd',
            'event_status_id' => BookingState::Bestaetigt->value,
            'cleaning_mail' => 1,
        ]))->assertRedirect(route('home').'#booking');

        $event = Event::sole();
        $this->assertNull($event->code);
        $this->assertNull($event->comment_intern);
        $this->assertFalse($event->external);
        $this->assertNull($event->user_id);
        $this->assertNull($event->bexio_invoice_id);
        $this->assertNull($event->glutz_user_id);
        $this->assertFalse($event->cleaning_mail);
        $this->assertSame(BookingState::Neu, $event->event_status_id);
    }

    public function test_booking_without_overnight_stay_is_rejected(): void
    {
        // Tagesaufenthalte gibt es nicht mehr; das Formular verlangt mindestens eine Nacht, der Server auch.
        $date = now()->addMonth()->toDateString();

        $this->from(route('home'))->post('/event/create', $this->bookingData([
            'total_days' => 0,
            'date' => $date,
            'start_date' => $date,
            'end_date' => $date,
        ]))->assertRedirect(route('home').'#booking')
            ->assertSessionHasErrors(['total_days' => 'Es muss mindestens eine Übernachtung gewählt werden.'], null, 'event');

        $this->assertSame(0, Event::count());
    }

    /**
     * Wert, den der Browser für die angehakte Hausordnung schickt: das value-Attribut der Checkbox auf der Startseite.
     */
    private function termsValueFromForm(): string
    {
        BookingScenario::create();
        preg_match('/<input type="checkbox" name="terms"[^>]*value="([^"]*)"/', $this->get('/')->getContent(), $match);
        $this->assertNotEmpty($match, 'Checkbox "terms" nicht gefunden');

        return $match[1];
    }

    public function test_accepted_terms_as_sent_by_browser(): void
    {
        $terms = $this->termsValueFromForm();
        $before = Event::count();

        $this->from(route('home'))->post('/event/create', $this->bookingData(['terms' => $terms]))
            ->assertSessionHasNoErrors();

        $this->assertSame($before + 1, Event::count());
    }

    public function test_unchecked_terms_are_rejected(): void
    {
        $data = $this->bookingData();
        unset($data['terms']);

        $this->from(route('home'))->post('/event/create', $data)->assertSessionHasErrors('terms', null, 'event');
        $this->get('/')->assertSee('Bitte akzeptiere die Hausordnung.');
    }

    public function test_inputs_are_kept_after_validation_error(): void
    {
        BookingScenario::create();

        // Keine Personen: Fehler, aber die Eingaben dürfen nicht verloren gehen.
        $this->from(route('home'))->post('/event/create', $this->bookingData([
            'total_people' => 0,
            'positions' => [30 => 0, 40 => 0],
            'comment' => 'Wir kommen mit Velo',
        ]))->assertSessionHasErrors('total_people', null, 'event');

        $this->get('/')
            ->assertSee('Für die Buchung braucht es mindestens 1 Person.')
            ->assertSee('value="Muster"', false)
            ->assertSee('value="Dorfstrasse"', false)
            ->assertSee('value="max@example.com"', false)
            ->assertSee('value="079 123-45 67"', false)
            ->assertSee('>Wir kommen mit Velo</textarea>', false);
    }

    /**
     * HTML eines Formulars der Startseite anhand seiner Ziel-URL.
     */
    private function formHtml(string $html, string $action): string
    {
        preg_match('#<form[^>]*action="'.preg_quote($action, '#').'"[^>]*>.*?</form>#s', $html, $form);
        $this->assertNotEmpty($form, "Formular $action nicht gefunden");

        return $form[0];
    }

    public function test_forms_send_their_scope(): void
    {
        BookingScenario::create();
        $html = $this->get('/')->getContent();

        $this->assertStringContainsString('<input type="hidden" name="_form_scope" value="event">', $this->formHtml($html, route('event.create')));
        $this->assertStringContainsString('<input type="hidden" name="_form_scope" value="contact">', $this->formHtml($html, route('contacts.store')));
    }

    public function test_booking_error_does_not_fill_contact_form(): void
    {
        BookingScenario::create();

        // Beide Formulare haben Felder "name" und "email"; die alte Eingabe gehört nur zum abgeschickten Formular.
        $this->from(route('home'))->post('/event/create', $this->bookingData(['total_people' => 0]));
        $html = $this->get('/')->getContent();

        $this->assertStringContainsString('value="Muster"', $this->formHtml($html, route('event.create')));
        $contact = $this->formHtml($html, route('contacts.store'));
        $this->assertStringNotContainsString('value="Muster"', $contact);
        $this->assertStringNotContainsString('value="max@example.com"', $contact);
    }

    public function test_contact_error_does_not_fill_booking_form(): void
    {
        BookingScenario::create();

        $this->from(route('home'))->post('/contacts', ['name' => 'Kontakt Person', 'email' => 'kontakt@example.com', '_form_scope' => 'contact']);
        $html = $this->get('/')->getContent();

        $this->assertStringContainsString('value="Kontakt Person"', $this->formHtml($html, route('contacts.store')));
        $booking = $this->formHtml($html, route('event.create'));
        $this->assertStringNotContainsString('value="Kontakt Person"', $booking);
        $this->assertStringNotContainsString('value="kontakt@example.com"', $booking);
    }

    public function test_page_restores_selected_dates_after_validation_error(): void
    {
        BookingScenario::create();
        $start = now()->addMonth()->toDateString();
        $end = now()->addMonth()->addDays(2)->toDateString();

        $this->from(route('home'))->post('/event/create', $this->bookingData(['total_people' => 0]));
        $html = $this->get('/')->getContent();

        // Die versteckten Felder tragen die Auswahl; das Kalender-Skript markiert sie und öffnet das Formular.
        $this->assertMatchesRegularExpression('/<input type="hidden" name="start_date"[^>]*value="'.$start.'"/', $html);
        $this->assertMatchesRegularExpression('/<input type="hidden" name="end_date"[^>]*value="'.$end.'"/', $html);
        $this->assertStringContainsString("restoreBooking()\n", $html);
        $this->assertMatchesRegularExpression('/if \(true\) \{\s*wizard_step\(2\);/', $html);
    }

    public function test_fresh_page_does_not_open_the_form(): void
    {
        BookingScenario::create();

        $this->assertMatchesRegularExpression('/if \(false\) \{\s*wizard_step\(2\);/', $this->get('/')->getContent());
    }

    public function test_incomplete_booking_is_rejected_without_server_error(): void
    {
        // Bots schicken leere oder halbe Formulare; das darf keinen 500er geben.
        $this->from(route('home'))->post('/event/create', [])
            ->assertRedirect(route('home').'#booking')
            ->assertSessionHasErrors(['name', 'street', 'zipcode', 'city', 'email', 'total_days', 'total_people', 'terms'], null, 'event');
        $this->from(route('home'))->post('/event/create', $this->bookingData(['start_date' => null]))
            ->assertSessionHasErrors('start_date', null, 'event');

        $this->assertSame(0, Event::count());
    }

    public function test_booking_without_optional_fields(): void
    {
        $data = $this->bookingData();
        unset($data['group'], $data['telephone'], $data['firstname'], $data['comment']);

        $this->from(route('home'))->post('/event/create', $data)->assertSessionHasNoErrors();

        $this->assertSame('', Event::sole()->phonenumber_query);
    }

    public function test_invalid_zipcode_is_rejected(): void
    {
        $this->from(route('home'))->post('/event/create', $this->bookingData(['zipcode' => 123]))
            ->assertRedirect(route('home').'#booking')
            ->assertSessionHasErrors('zipcode', null, 'event');

        $this->assertSame(0, Event::count());
    }

    public function test_more_than_19_people_is_rejected(): void
    {
        $this->from(route('home'))->post('/event/create', $this->bookingData(['total_people' => 20]))
            ->assertSessionHasErrors('total_people', null, 'event');

        $this->assertSame(0, Event::count());
    }

    public function test_discount_sent_by_browser_is_ignored_for_later_bookings(): void
    {
        config(['app.discount_enabled' => true]);

        $this->from(route('home'))->post('/event/create', $this->bookingData(['discount' => 50]));

        $this->assertEquals(0, Event::sole()->discount);
    }

    public function test_short_notice_booking_gets_discount_when_enabled(): void
    {
        config(['app.discount_enabled' => true]);

        $this->from(route('home'))->post('/event/create', $this->bookingData([
            'start_date' => today()->addDays(7)->toDateString(),
            'end_date' => today()->addDays(9)->toDateString(),
            'discount' => 0,
        ]));

        $this->assertEquals(50, Event::sole()->discount);
    }

    public function test_no_discount_when_disabled(): void
    {
        config(['app.discount_enabled' => false]);

        $this->from(route('home'))->post('/event/create', $this->bookingData([
            'start_date' => today()->addDays(7)->toDateString(),
            'end_date' => today()->addDays(9)->toDateString(),
            'discount' => 50,
        ]));

        $this->assertEquals(0, Event::sole()->discount);
    }
}
