<?php

namespace Tests\Feature\Security;

use App\Models\BexioToken;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Bexio verbinden (OAuth): /bexio/connect (Basic-Auth) leitet zu Bexio, Bexio ruft /bexio/callback auf.
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
        $redirect = $this->withBasicAuth('deploy', 'deploy-secret')->get('/bexio/connect')->headers->get('Location');
        parse_str(parse_url($redirect, PHP_URL_QUERY), $query);

        $this->assertNotEmpty($query['state'] ?? null);

        $this->get('/bexio/callback?code=abc&state='.$query['state'])->assertOk();

        $this->assertSame('access', BexioToken::sole()->access_token);
        Http::assertSent(fn ($request) => $request['code'] === 'abc');
    }

    public function test_callback_without_state_is_rejected(): void
    {
        $this->get('/bexio/callback?code=fremder-code')->assertForbidden();

        $this->assertSame(0, BexioToken::count());
        Http::assertNothingSent();
    }

    public function test_callback_with_foreign_state_is_rejected(): void
    {
        $this->withBasicAuth('deploy', 'deploy-secret')->get('/bexio/connect');

        $this->get('/bexio/callback?code=fremder-code&state=geraten')->assertForbidden();

        $this->assertSame(0, BexioToken::count());
        Http::assertNothingSent();
    }

    public function test_state_can_only_be_used_once(): void
    {
        $redirect = $this->withBasicAuth('deploy', 'deploy-secret')->get('/bexio/connect')->headers->get('Location');
        parse_str(parse_url($redirect, PHP_URL_QUERY), $query);

        $this->get('/bexio/callback?code=abc&state='.$query['state'])->assertOk();
        $this->get('/bexio/callback?code=zweiter&state='.$query['state'])->assertForbidden();

        Http::assertSentCount(1);
    }
}
