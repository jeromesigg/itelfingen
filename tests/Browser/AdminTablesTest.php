<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Support\BookingScenario;

/**
 * Admin-Tabellen (DataTable.vue) im echten Browser: Aktionen ohne Neuladen und Sortieren.
 */
class AdminTablesTest extends DuskTestCase
{
    public function test_marking_contact_done_updates_row_without_reload(): void
    {
        $s = BookingScenario::create();

        $this->browse(function (Browser $browser) use ($s) {
            $browser->loginAs($s->user('verwalter'))
                ->visit('/admin/contacts')
                ->waitForText($s->contact->name)
                // Der Button in der Zeile, nicht der gleichnamige Filter über der Tabelle
                ->click('#tableBody button')
                ->waitForText('Anfrage als bearbeitet markiert.')
                ->assertMissing('#tableBody button');
        });

        $this->assertTrue((bool) $s->contact->fresh()->done);
    }

    public function test_refusing_application_asks_and_updates_row(): void
    {
        $s = BookingScenario::create();

        $this->browse(function (Browser $browser) use ($s) {
            $browser->loginAs($s->user('verwalter'))
                ->visit('/admin/applications')
                ->waitForText($s->application->firstname)
                ->press('Ablehnen')
                ->waitFor('.swal2-confirm')
                ->assertSee('ablehnen?');

            // Abbrechen ändert nichts
            $browser->click('.swal2-cancel')->waitUntilMissing('.swal2-container');
            $this->assertFalse((bool) $s->application->fresh()->refuse);

            $browser->press('Ablehnen')
                ->waitFor('.swal2-confirm')
                ->click('.swal2-confirm')
                ->waitForText('Bewerbung abgelehnt.')
                ->assertDontSeeIn('#tableBody', 'Ablehnen');
        });

        $this->assertTrue((bool) $s->application->fresh()->refuse);
    }

    public function test_bookings_sort_by_date_in_both_directions(): void
    {
        $s = BookingScenario::create();
        $s->event->update(['start_date' => '2030-11-11', 'end_date' => '2030-11-13']);
        $s->confirmedEvent->update(['start_date' => '2031-02-01', 'end_date' => '2031-02-03']);
        // Übrige Buchungen des Szenarios: vergangen bzw. storniert, beide stehen nicht in "Ab Heute / Alle"
        $first = '11.11.2030';
        $last = '01.02.2031';
        // Vom Server kommen die Buchungen nach Datum sortiert; erst absteigend zeigt, dass der Klick wirkt

        $this->browse(function (Browser $browser) use ($s, $first, $last) {
            $browser->loginAs($s->user('verwalter'))
                ->visit('/admin/events')
                ->waitFor('#tableBody tr td')
                ->assertSeeIn('#tableBody', $first)
                ->assertSeeIn('#tableBody', $last)
                ->click('th[aria-sort]:first-child')
                ->assertAttribute('th[aria-sort]:first-child', 'aria-sort', 'ascending')
                ->assertSeeIn('#tableBody tr:first-child td:first-child', $first)
                ->click('th[aria-sort]:first-child')
                ->assertAttribute('th[aria-sort]:first-child', 'aria-sort', 'descending')
                ->assertSeeIn('#tableBody tr:first-child td:first-child', $last);
        });
    }
}
