<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

/**
 * Admin-Bereich als installierbare App: Manifest, Icons und Service Worker.
 */
class AdminWebAppTest extends TestCase
{
    public function test_manifest_opens_installed_app_on_admin_dashboard(): void
    {
        $response = $this->get('/app.webmanifest');

        $response->assertOk()
            ->assertHeader('Content-Type', 'application/manifest+json')
            ->assertJson([
                'start_url' => '/admin',
                'scope' => '/',
                'display' => 'standalone',
            ]);
    }

    public function test_manifest_shortcuts_open_pages_the_team_may_see(): void
    {
        $team = User::factory()->team()->create();
        $shortcuts = $this->get('/app.webmanifest')->json('shortcuts');

        foreach ($shortcuts as $shortcut) {
            $this->actingAs($team)->get($shortcut['url'])->assertOk();
        }
    }

    public function test_all_app_icons_exist(): void
    {
        $icons = collect($this->get('/app.webmanifest')->json('icons'))->pluck('src')
            ->push('/img/pwa/apple-touch-icon.png');

        foreach ($icons as $icon) {
            $this->assertFileExists(public_path($icon));
        }
    }

    public function test_admin_layout_links_manifest_and_registers_service_worker(): void
    {
        $response = $this->actingAs(User::factory()->team()->create())->get('/admin');

        $response->assertOk()
            ->assertSee('<link rel="manifest" href="'.route('webmanifest').'">', false)
            ->assertSee("register('/admin-sw.js'", false);
        $this->assertFileExists(public_path('admin-sw.js'));
        $this->assertFileExists(public_path('offline.html'));
    }
}
