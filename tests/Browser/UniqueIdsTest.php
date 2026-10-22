<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Support\BookingScenario;

/**
 * Jede id nur einmal pro Seite: Sonst zeigt <label for> auf das Feld eines anderen Formulars,
 * Screenreader lesen falsche Beschriftungen vor und Skripte greifen auf das falsche Element zu.
 * Geprüft wird das fertige DOM, also auch, was Vue rendert.
 */
class UniqueIdsTest extends DuskTestCase
{
    public function test_ids_are_unique_on_every_page(): void
    {
        $s = BookingScenario::create();
        $public = ['/', '/about_us', '/applications', '/faq', '/impressum', '/login', '/bookings/login', "/bookings/{$s->event->uuid}"];
        $admin = [
            '/admin', '/admin/applications', "/admin/applications/{$s->application->id}", '/admin/bookings', '/admin/contacts',
            '/admin/events', '/admin/events/create', "/admin/events/{$s->event->id}/edit", '/admin/faqs', '/admin/newsletter',
            '/admin/pictures', '/admin/positions', '/admin/users', "/admin/users/{$s->verwalter->id}/edit",
        ];
        $script = <<<'JS'
            const seen = {};
            document.querySelectorAll('[id]').forEach((element) => { seen[element.id] = (seen[element.id] ?? 0) + 1; });
            return Object.entries(seen).filter(([, count]) => count > 1).map(([id, count]) => `${id} (${count}×)`);
        JS;

        $duplicates = [];
        $this->browse(function (Browser $browser) use ($public, $admin, $s, $script, &$duplicates) {
            foreach ($public as $url) {
                $found = $browser->visit($url)->pause(300)->script($script)[0];
                if ($found) {
                    $duplicates[$url] = $found;
                }
            }
            $browser->loginAs($s->user('admin'));
            foreach ($admin as $url) {
                $found = $browser->visit($url)->pause(300)->script($script)[0];
                if ($found) {
                    $duplicates[$url] = $found;
                }
            }
        });

        $this->assertSame([], $duplicates, 'Doppelte ids: '.json_encode($duplicates, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }
}
