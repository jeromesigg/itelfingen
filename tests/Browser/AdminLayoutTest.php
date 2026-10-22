<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\DuskTestCase;
use Tests\Support\BookingScenario;

/**
 * Admin-Layout: die fixe Navbar oben darf weder den Inhalt noch die Seitenleiste überdecken.
 * Bricht die Navbar auf dem Handy in zwei Zeilen um, wird sie höher als der Platz, den main und aside freihalten.
 */
class AdminLayoutTest extends DuskTestCase
{
    /** @return array<string, array{0: int}> */
    public static function widths(): array
    {
        return [
            'kleines Handy' => [360],
            'Handy' => [412],
            'Tablet' => [768],
            'Desktop' => [1280],
        ];
    }

    #[DataProvider('widths')]
    public function test_top_navbar_does_not_cover_content(int $width): void
    {
        $s = BookingScenario::create();
        $admin = $s->user('admin');
        $admin->update(['username' => 'jerome.sigg']);

        $this->browse(function (Browser $browser) use ($admin, $width) {
            $browser->resize($width, 900)
                ->loginAs($admin)
                ->visit('/admin')
                ->waitFor('main');

            if ($width < 768) {
                $browser->click('[data-drawer-toggle="drawer-navigation"]')->pause(400);
            }

            [$navBottom, $contentTop, $sidebarTop] = $browser->script(<<<'JS'
                return [
                    document.querySelector('nav').getBoundingClientRect().bottom,
                    document.querySelector('main > *').getBoundingClientRect().top,
                    document.querySelector('#drawer-navigation a').getBoundingClientRect().top,
                ];
                JS)[0];

            $this->assertLessThanOrEqual($contentTop, $navBottom, 'Navbar überdeckt den Inhalt');
            $this->assertLessThanOrEqual($sidebarTop, $navBottom, 'Navbar überdeckt die Seitenleiste');
        });
    }
}
