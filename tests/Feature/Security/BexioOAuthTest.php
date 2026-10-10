<?php

namespace Tests\Feature\Security;

use App\Models\BexioToken;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Tests\Support\BookingScenario;
use Tests\TestCase;

/**
 * Bexio verbinden (OAuth): admin/bexio/connect (nur Admin, Knopf auf der Wartungsseite) leitet zu Bexio,
 * Bexio ruft /bexio/callback auf.
 * Der Callback darf nur einen Code annehmen, den unsere eigene Weiterleitung angefordert hat (state).
 */
class BexioOAuthTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Http::fake([
            'auth.bexio.com/*' => Http::response([
                'access_token' => 'access',
                'refresh_token' => 'refresh',
                'expires_in' => 3600,
            ]),
        ]);
    }

    public function test_connect_and_callback_store_token(): void
    {
        $redirect = $this->actingAs($this->admin())->get('/admin/bexio/connect')->headers->get('Location');
        parse_str(parse_url($redirect, PHP_URL_QUERY), $query);

        $this->assertNotEmpty($query['state'] ?? null);

        $this->get('/bexio/callback?code=abc&state='.$query['state'])->assertRedirect(route('admin.maintenance'));

        $this->assertSame('access', BexioToken::sole()->access_token);
        Http::assertSent(fn ($request) => $request['code'] === 'abc');
    }

    public function test_only_admins_can_connect_bexio(): void
    {
        $s = BookingScenario::create();

        foreach (['verwalter', 'team', 'gast'] as $role) {
            $this->actingAs($s->user($role))->get('/admin/bexio/connect')->assertForbidden();
        }
        $this->app['auth']->forgetGuards();
        $this->get('/admin/bexio/connect')->assertRedirect('/login');
    }

    public function test_maintenance_page_shows_connection_status_and_button(): void
    {
        $this->actingAs($this->admin())->get('/admin/maintenance')
            ->assertSee('Nicht verbunden')
            ->assertSee(route('admin.bexio.connect'));

        BexioToken::create(['access_token' => 'a', 'refresh_token' => 'r', 'expires_at' => now()->addHour()]);

        $this->actingAs($this->admin())->get('/admin/maintenance')->assertSee('Verbunden');
    }

    public function test_callback_without_state_is_rejected(): void
    {
        $this->get('/bexio/callback?code=fremder-code')->assertForbidden();

        $this->assertSame(0, BexioToken::count());
        Http::assertNothingSent();
    }

    public function test_callback_with_foreign_state_is_rejected(): void
    {
        $this->actingAs($this->admin())->get('/admin/bexio/connect');

        $this->get('/bexio/callback?code=fremder-code&state=geraten')->assertForbidden();

        $this->assertSame(0, BexioToken::count());
        Http::assertNothingSent();
    }

    private function admin(): User
    {
        return User::find(1);
    }

    public function test_state_can_only_be_used_once(): void
    {
        $redirect = $this->actingAs($this->admin())->get('/admin/bexio/connect')->headers->get('Location');
        parse_str(parse_url($redirect, PHP_URL_QUERY), $query);

        $this->get('/bexio/callback?code=abc&state='.$query['state'])->assertRedirect(route('admin.maintenance'));
        $this->get('/bexio/callback?code=zweiter&state='.$query['state'])->assertForbidden();

        Http::assertSentCount(1);
    }
}
