<?php

namespace Tests\Browser;

use App\Enums\ArchiveState;
use App\Models\Event;
use App\Models\PricelistPosition;
use Carbon\Carbon;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Support\BookingScenario;

/**
 * Buchungsassistent der Startseite (BookingWizard.vue) im echten Browser: Kalender, Formular, Absenden.
 */
class BookingWizardTest extends DuskTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        BookingScenario::create();
        // Personen wie in der echten Preisliste (110–150); die Codes 30/40 des TestingSeeders zeigt der Assistent nicht als Eingabe
        PricelistPosition::where('bexio_code', '<', 100)->where('bexio_code', '!=', 20)->update(['show' => false]);
        PricelistPosition::where('bexio_code', 20)->update(['show' => true]);
        PricelistPosition::create([
            'bexio_code' => 120, 'bexio_id' => 1120, 'name' => 'Erwachsene, Normaltarif', 'price' => 60,
            'show' => true, 'archive_status_id' => ArchiveState::Aktiv,
        ]);
    }

    public function test_guest_sends_booking_request(): void
    {
        $start = now()->addMonthNoOverflow()->startOfMonth()->addDays(9);
        $end = $start->copy()->addDays(3);

        // Pauschale (Code 20, im TestingSeeder 300.-) + 4 Erwachsene × 60.- × 3 Nächte
        $total = (int) PricelistPosition::where('bexio_code', 20)->value('price') + 4 * 60 * 3;

        $this->browse(function (Browser $browser) use ($start, $end, $total) {
            $browser->visit('/');
            $this->clickCentered($browser, '[data-date="'.$start->toDateString().'"] button');
            $this->clickCentered($browser, '[data-date="'.$end->toDateString().'"] button');
            $browser->assertSee('3 Nächte');

            $this->clickCentered($browser, '#booking button.bg-gladegreen');
            $browser->waitFor('#name')
                ->type('#firstname', 'Max')
                ->type('#name', 'Muster')
                ->type('#street', 'Dorfstrasse')
                ->type('#zipcode', '8000')
                ->type('#city', 'Zürich')
                ->type('#email', 'max@example.com')
                // Personen zeichnen den Assistenten neu; die Eingaben darüber müssen bleiben
                ->type('#position_120', '4')
                ->assertInputValue('#name', 'Muster')
                ->assertInputValue('#email', 'max@example.com')
                ->check('#terms');
            // Tausendertrennzeichen je nach ICU-Version des Browsers ' oder ’, darum nur die Ziffern vergleichen
            $shown = preg_replace('/\D/', '', $browser->text('#booking tfoot td.whitespace-nowrap'));
            $this->assertSame((string) $total, $shown);

            $this->clickCentered($browser, '#booking button[type="submit"]');
            $browser->waitForText('Vielen Dank für die Buchung');
        });

        $event = Event::where('email', 'max@example.com')->sole();
        $this->assertSame('Muster', $event->name);
        // SQLite speichert das Carbon-Datum des Controllers mit Uhrzeit
        $this->assertSame($start->toDateString(), Carbon::parse($event->start_date)->toDateString());
        $this->assertSame(3, (int) $event->total_days);
        $this->assertSame(4, (int) $event->total_people);
    }

    public function test_validation_error_keeps_input_and_reopens_form(): void
    {
        $start = now()->addMonthNoOverflow()->startOfMonth()->addDays(9);

        $this->browse(function (Browser $browser) use ($start) {
            $browser->visit('/');
            $this->clickCentered($browser, '[data-date="'.$start->toDateString().'"] button');
            $this->clickCentered($browser, '[data-date="'.$start->copy()->addDays(2)->toDateString().'"] button');
            $this->clickCentered($browser, '#booking button.bg-gladegreen');
            $browser->waitFor('#name')
                ->type('#name', 'Muster')
                ->type('#street', 'Dorfstrasse')
                ->type('#zipcode', '8000')
                ->type('#city', 'Zürich')
                ->type('#email', 'max@example.com')
                ->check('#terms');

            // Ohne Personen lehnt der Server ab
            $this->clickCentered($browser, '#booking button[type="submit"]');
            $browser->waitForText('Für die Buchung braucht es mindestens 1 Person.')
                ->assertVisible('#name')
                ->assertInputValue('#name', 'Muster')
                ->assertInputValue('#email', 'max@example.com');
        });

        $this->assertSame(0, Event::where('email', 'max@example.com')->count());
    }
}
