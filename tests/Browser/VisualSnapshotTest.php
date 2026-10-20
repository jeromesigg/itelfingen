<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Support\BookingScenario;

/**
 * Ganzseitige Screenshots aller Seiten für einen Vorher/Nachher-Vergleich, z. B. beim Aufräumen von CSS.
 * Läuft nur mit SNAPSHOT_SET, sonst übersprungen. Ablauf siehe tests/README.md (Abschnitt Browser).
 */
class VisualSnapshotTest extends DuskTestCase
{
    public function test_snapshots(): void
    {
        $set = getenv('SNAPSHOT_SET');
        if (! $set) {
            $this->markTestSkipped('Nur für den CSS-Vergleich (SNAPSHOT_SET setzen).');
        }

        // Gleiche Testdaten (und Zeitstempel) in beiden Läufen
        fake()->seed(20261020);
        $this->travelTo(now()->startOfDay()->setTime(12, 0));
        $s = BookingScenario::create();

        $public = ['/', '/about_us', '/applications', '/faq', '/impressum', '/login', '/bookings/login', "/bookings/{$s->event->uuid}" => 'kundenseite'];
        $admin = [
            '/admin', '/admin/applications', "/admin/applications/{$s->application->id}", '/admin/bookings', '/admin/changes',
            '/admin/checkpoints', '/admin/contacts', '/admin/events', '/admin/events/create', "/admin/events/{$s->event->id}/edit",
            '/admin/faq_chapters', '/admin/faqs', '/admin/histories', '/admin/homepages', '/admin/maintenance', '/admin/newsletter',
            '/admin/people', '/admin/pictures', '/admin/positions', '/admin/rooms', '/admin/testimonials', '/admin/users',
            "/admin/users/{$s->verwalter->id}/edit",
        ];

        $this->browse(function (Browser $browser) use ($set, $public, $admin, $s) {
            foreach ($public as $url => $name) {
                $url = is_string($url) ? $url : $name;
                $this->snapshot($browser, $set, $url, 1440, $name);
                $this->snapshot($browser, $set, $url, 390, $name);
            }
            $browser->loginAs($s->user('admin'));
            foreach ($admin as $url) {
                $this->snapshot($browser, $set, $url, 1440);
            }
        });
    }

    private function snapshot(Browser $browser, string $set, string $url, int $width, ?string $name = null): void
    {
        $browser->resize($width, 900)->visit($url)->pause(1200);
        // Ganze Seite: Fensterhöhe an den Inhalt anpassen, bis sie sich nicht mehr ändert (min-h-screen wächst mit)
        $height = 0;
        for ($i = 0; $i < 4; $i++) {
            $measured = min((int) $browser->script('return document.body.scrollHeight')[0], 12000);
            if ($measured === $height) {
                break;
            }
            $height = $measured;
            $browser->resize($width, max($height, 900))->pause(500);
        }

        $name = trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($name ?? $url)), '-') ?: 'home';
        $browser->screenshot("{$set}/{$width}-{$name}");
    }
}
