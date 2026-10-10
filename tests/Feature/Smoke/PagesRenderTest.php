<?php

namespace Tests\Feature\Smoke;

use App\Models\BexioToken;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Support\BookingScenario;
use Tests\TestCase;

/**
 * Jede Seite, die das Admin-Menü einer Rolle verlinkt, muss für diese Rolle laden (200).
 * Die Links werden aus der Navigation gelesen; neue Menüeinträge sind automatisch dabei.
 */
class PagesRenderTest extends TestCase
{
    public static function roles(): array
    {
        return ['team' => ['team'], 'verwalter' => ['verwalter'], 'admin' => ['admin']];
    }

    #[DataProvider('roles')]
    public function test_every_linked_admin_page_renders(string $role): void
    {
        BexioToken::create(['access_token' => 'token', 'refresh_token' => 'refresh', 'expires_at' => now()->addHour()]);
        Http::fake(['api.bexio.com/*' => Http::response([])]);
        $s = BookingScenario::create();

        $links = $this->navigationLinks($this->actingAs($s->user($role))->get('/admin')->assertOk()->getContent());
        $this->assertNotEmpty($links);

        $failures = [];
        foreach ($links as $link) {
            $status = $this->actingAs($s->user($role))->get($link)->getStatusCode();
            if ($status !== 200) {
                $failures[] = "$link → $status";
            }
        }

        $this->assertSame([], $failures, "Menü-Links, die für $role nicht laden:\n".implode("\n", $failures));
    }

    public function test_public_pages_render(): void
    {
        $s = BookingScenario::create();

        foreach (['/', '/about_us', '/faq', '/impressum', '/applications', '/bookings/login', '/login', "/bookings/{$s->event->uuid}/checklist"] as $page) {
            $this->get($page)->assertOk();
        }
    }

    /**
     * Interne Links der Admin-Seitennavigation (ohne "Zur Seite").
     *
     * @return list<string>
     */
    private function navigationLinks(string $html): array
    {
        preg_match('/<aside.*?<\/aside>/s', $html, $aside);
        preg_match_all('/href="(?:http:\/\/localhost)?(\/[^"#]*)"/', $aside[0] ?? '', $matches);

        return array_values(array_unique(array_filter($matches[1], fn ($link) => $link !== '/')));
    }
}
