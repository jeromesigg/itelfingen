<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Support\BookingScenario;

/**
 * Schalter "Push-Meldungen" im Profil: Modul und Service Worker laufen im echten Browser.
 * An- und Abmelden prüft tests/js/push-toggle.test.js; der Push-Dienst von Google ist aus dem Container nicht zuverlässig erreichbar.
 */
class PushToggleTest extends DuskTestCase
{
    public function test_profile_offers_to_turn_push_on(): void
    {
        $verwalter = BookingScenario::create()->user('verwalter');

        $this->browse(function (Browser $browser) use ($verwalter) {
            $browser->loginAs($verwalter)
                ->visit("/admin/users/{$verwalter->id}/edit")
                ->waitForText('Auf diesem Gerät ausgeschaltet.')
                ->assertVisible('[data-action="push-subscribe"]')
                ->assertMissing('[data-action="push-unsubscribe"]');
        });
    }
}
