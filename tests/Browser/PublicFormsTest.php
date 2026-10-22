<?php

namespace Tests\Browser;

use App\Models\Application;
use App\Models\City;
use App\Models\Contact;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Support\BookingScenario;

/**
 * Öffentliche Formulare im echten Browser: Kontakt, Bewerbung mit PLZ-Vorschlägen, Kundenbereich.
 */
class PublicFormsTest extends DuskTestCase
{
    public function test_contact_form_sends_message(): void
    {
        BookingScenario::create();

        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->type('#contact_form [name="name"]', 'Erika Muster')
                ->type('#contact_form [name="email"]', 'erika@example.com')
                ->type('#contact_form [name="subject"]', 'Frage zum Haus')
                ->type('#contact_form [name="content"]', 'Gibt es einen Grill vor dem Haus?');
            $this->clickCentered($browser, '#contact_form button[type="submit"]');
            $browser->waitForText('Vielen Dank für die Nachricht');
        });

        $contact = Contact::where('email', 'erika@example.com')->sole();
        $this->assertSame('Frage zum Haus', $contact->subject);
        $this->assertFalse((bool) $contact->done);
    }

    public function test_application_fills_city_from_zip_code(): void
    {
        BookingScenario::create();
        City::create(['name' => 'Meierskappel', 'plz' => 6344]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/applications')
                ->type('#application_form [name="firstname"]', 'Max')
                ->type('#application_form [name="name"]', 'Muster')
                ->type('#application_form [name="street"]', 'Dorfstrasse')
                ->type('#application_form [name="zipcode"]', '6344')
                // city-search.js: eindeutiger Treffer füllt die Ortschaft
                ->waitUsing(5, 100, fn () => $browser->value('#application_form [name="city"]') === 'Meierskappel')
                ->type('#application_form [name="email"]', 'max@example.com')
                ->type('#application_form [name="why"]', 'Ich mag das Haus und möchte mithelfen.')
                ->check('#statutes');
            $this->clickCentered($browser, '#application_form button[type="submit"]');
            $browser->waitForText('Vielen Dank für deine Bewerbung');
        });

        $application = Application::where('email', 'max@example.com')->sole();
        $this->assertSame('Meierskappel', $application->city);
        $this->assertSame('6344', (string) $application->plz);
    }

    public function test_customer_opens_booking_with_number_and_zip_code(): void
    {
        $s = BookingScenario::create();
        $s->event->update(['plz' => 6344]);

        $this->browse(function (Browser $browser) use ($s) {
            $browser->visit('/bookings/login')
                ->type('id', $s->event->number())
                ->type('plz', '6344')
                ->press('Zu meiner Buchung')
                ->waitForText('Deine Buchung Nr. '.$s->event->number())
                ->assertPathIs('/bookings/'.$s->event->uuid);
        });
    }

    public function test_customer_login_with_wrong_zip_code_keeps_number(): void
    {
        $s = BookingScenario::create();
        $s->event->update(['plz' => 6344]);

        $this->browse(function (Browser $browser) use ($s) {
            $browser->visit('/bookings/login')
                ->type('id', $s->event->number())
                ->type('plz', '8000')
                ->press('Zu meiner Buchung')
                ->waitForText('Die eingegebenen Daten sind nicht korrekt.')
                ->assertPathIs('/bookings/login')
                ->assertInputValue('id', $s->event->number())
                ->assertInputValue('plz', '');
        });
    }
}
