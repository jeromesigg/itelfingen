<?php

namespace Tests\Browser;

use Facebook\WebDriver\Chrome\ChromeDevToolsDriver;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Support\BookingScenario;

/**
 * Admin-Bereich als installierbare App: der Service Worker zeigt ohne Netz die Offline-Seite.
 */
class AdminWebAppTest extends DuskTestCase
{
    public function test_admin_shows_offline_page_without_network(): void
    {
        $admin = BookingScenario::create()->user('admin');

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)->visit('/admin');
            $scope = $browser->driver->executeAsyncScript(
                'navigator.serviceWorker.ready.then((registration) => arguments[0](registration.scope))'
            );
            $devTools = new ChromeDevToolsDriver($browser->driver);
            $devTools->execute('Network.enable');
            $devTools->execute('Network.emulateNetworkConditions', ['offline' => true, 'latency' => 0, 'downloadThroughput' => -1, 'uploadThroughput' => -1]);

            $browser->visit('/admin/events')->waitForText('Keine Verbindung');

            $this->assertSame(config('app.url').'/admin', $scope);
            $browser->assertSee('Erneut versuchen');
        });
    }
}
