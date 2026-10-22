<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Support\BookingScenario;

/**
 * Formular "Buchung bearbeiten" (resources/js/admin/event-form.js) im echten Browser.
 */
class EventEditTest extends DuskTestCase
{
    public function test_early_checkin_updates_calendar_and_total(): void
    {
        $s = BookingScenario::create();
        $event = $s->event;
        $event->update(['start_date' => '2030-11-11', 'end_date' => '2030-11-13', 'early_checkin' => false, 'late_checkout' => false]);
        $startDay = 'td[data-date="2030-11-11"]';

        $this->browse(function (Browser $browser) use ($s, $event, $startDay) {
            $browser->loginAs($s->user('verwalter'))
                ->visit("/admin/events/{$event->id}/edit")
                ->waitFor($startDay)
                // Anreise am Nachmittag: halber Tag
                ->assertAttributeContains($startDay, 'class', 'hk-agenda__day--FS');
            $total = $this->total($browser);

            $this->clickCentered($browser, '#early_checkin');
            // Kalender lädt neu: ganzer Tag; Pauschale Frühbezug/Spätabgabe (50.-) im Total
            $browser->waitUsing(5, 100, fn () => str_contains((string) $browser->attribute($startDay, 'class'), 'hk-agenda__day--SS'));
            $this->assertSame($total + 50, $this->total($browser));
        });
    }

    /** Angezeigtes Total in CHF (de-CH-Format, z. B. 1’010). */
    private function total(Browser $browser): int
    {
        return (int) preg_replace('/\D/', '', $browser->text('#total'));
    }
}
