<?php

namespace Tests;

use Database\Seeders\TestingSeeder;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Collection;
use Laravel\Dusk\Browser;
use Laravel\Dusk\TestCase as BaseTestCase;
use PHPUnit\Framework\Attributes\BeforeClass;
use RuntimeException;

/**
 * Basis der Browser-Tests (tests/Browser, sail dusk). Umgebung aus .env.dusk.local, Testdaten wie in den
 * Feature-Tests (TestingSeeder, BookingScenario), aber in einer SQLite-Datei, damit der Webserver sie sieht.
 */
abstract class DuskTestCase extends BaseTestCase
{
    use DatabaseMigrations;

    protected $seed = true;

    protected $seeder = TestingSeeder::class;

    #[BeforeClass]
    public static function prepare(): void
    {
        if (! static::runningInSail()) {
            static::startChromeDriver(['--port=9515']);
        }
    }

    /**
     * Läuft vor migrate:fresh: nie die Entwicklungs- oder Produktionsdatenbank leeren.
     */
    protected function beforeRefreshingDatabase(): void
    {
        $database = (string) config('database.connections.sqlite.database');
        if (config('database.default') !== 'sqlite' || ! str_ends_with($database, 'dusk.sqlite')) {
            throw new RuntimeException('Dusk läuft nur mit database/dusk.sqlite (.env.dusk.local), nicht mit '.config('database.default').'.');
        }
        if (! file_exists($database)) {
            touch($database);
        }
    }

    protected function driver(): RemoteWebDriver
    {
        $options = (new ChromeOptions)->addArguments(collect([
            $this->shouldStartMaximized() ? '--start-maximized' : '--window-size=1920,1080',
            '--disable-search-engine-choice-screen',
            '--disable-smooth-scrolling',
        ])->unless($this->hasHeadlessDisabled(), function (Collection $items) {
            return $items->merge(['--disable-gpu', '--headless=new']);
        })->all());

        return RemoteWebDriver::create(
            $_ENV['DUSK_DRIVER_URL'] ?? env('DUSK_DRIVER_URL') ?? 'http://localhost:9515',
            DesiredCapabilities::chrome()->setCapability(ChromeOptions::CAPABILITY, $options)
        );
    }

    /**
     * Element in die Mitte scrollen und anklicken; oben am Rand läge es unter dem fixierten Header.
     */
    protected function clickCentered(Browser $browser, string $selector): Browser
    {
        $browser->waitFor($selector)
            ->script('document.querySelector('.json_encode($selector).').scrollIntoView({ block: "center" })');

        return $browser->click($selector);
    }
}
