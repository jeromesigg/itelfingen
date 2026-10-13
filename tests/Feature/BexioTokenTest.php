<?php

namespace Tests\Feature;

use App\Models\BexioToken;
use App\Services\BexioAuthService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Tests\TestCase;

/**
 * Bexio-Zugang: Tokens verschlüsselt in der Datenbank, Erneuerung nur durch einen Prozess gleichzeitig.
 * Bexio gibt bei jeder Erneuerung einen neuen Refresh-Token aus und macht den alten ungültig.
 */
class BexioTokenTest extends TestCase
{
    private const MIGRATION = '2026_10_13_000000_encrypt_bexio_tokens.php';

    private function expiredToken(): BexioToken
    {
        return BexioToken::create(['access_token' => 'alt', 'refresh_token' => 'refresh-alt', 'expires_at' => now()->subMinute()]);
    }

    private function fakeTokenEndpoint(): void
    {
        Http::fake(['auth.bexio.com/*' => Http::response(['access_token' => 'neu', 'refresh_token' => 'refresh-neu', 'expires_in' => 3600])]);
    }

    public function test_tokens_are_stored_encrypted(): void
    {
        BexioToken::create(['access_token' => 'geheim', 'refresh_token' => 'auch-geheim', 'expires_at' => now()->addHour()]);

        $row = DB::table('bexio_tokens')->first();
        $this->assertNotSame('geheim', $row->access_token);
        $this->assertSame('geheim', Crypt::decryptString($row->access_token));
        $this->assertSame('auch-geheim', BexioToken::sole()->refresh_token);
    }

    public function test_migration_encrypts_existing_plaintext_tokens_once(): void
    {
        // So liegen die Tokens heute in Prod
        DB::table('bexio_tokens')->insert(['access_token' => 'klartext', 'refresh_token' => 'refresh-klartext', 'expires_at' => now()->addHour()]);
        $migration = require database_path('migrations/'.self::MIGRATION);

        $migration->encryptExisting();
        $migration->encryptExisting();

        $this->assertSame('klartext', BexioToken::sole()->access_token);
        $this->assertSame('refresh-klartext', BexioToken::sole()->refresh_token);
    }

    public function test_expired_token_is_refreshed_and_rotation_saved(): void
    {
        $this->expiredToken();
        $this->fakeTokenEndpoint();

        $this->assertSame('neu', app(BexioAuthService::class)->getValidAccessToken());

        $this->assertSame('refresh-neu', BexioToken::sole()->refresh_token);
        Http::assertSent(fn (Request $r) => $r['grant_type'] === 'refresh_token' && $r['refresh_token'] === 'refresh-alt');
        // Sperre wieder frei
        $this->assertTrue(Cache::lock('bexio-token-refresh')->get());
    }

    public function test_second_process_uses_token_refreshed_meanwhile(): void
    {
        // Prozess B liest den abgelaufenen Token; bevor er die Sperre bekommt, hat Prozess A schon erneuert.
        $token = $this->expiredToken();
        $this->fakeTokenEndpoint();
        $reads = 0;
        Event::listen('eloquent.retrieved: '.BexioToken::class, function () use (&$reads, $token) {
            if (++$reads === 1) {
                DB::table('bexio_tokens')->where('id', $token->id)->update([
                    'access_token' => Crypt::encryptString('von-prozess-a'),
                    'refresh_token' => Crypt::encryptString('refresh-von-a'),
                    'expires_at' => now()->addHour(),
                ]);
            }
        });

        $this->assertSame('von-prozess-a', app(BexioAuthService::class)->getValidAccessToken());

        // Kein zweiter Refresh: der hätte den Refresh-Token von A mit einem veralteten überschrieben
        Http::assertNothingSent();
    }

    public function test_failed_refresh_keeps_token_and_releases_lock(): void
    {
        $this->expiredToken();
        Http::fake(['auth.bexio.com/*' => Http::response(['error' => 'invalid_grant'], 400)]);

        try {
            app(BexioAuthService::class)->getValidAccessToken();
            $this->fail('Erneuerung hätte scheitern müssen');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('neu autorisieren', $e->getMessage());
        }

        $this->assertSame('refresh-alt', BexioToken::sole()->refresh_token);
        $this->assertTrue(Cache::lock('bexio-token-refresh')->get());
    }
}
