<?php

namespace Tests\Feature\Security;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Support\BookingScenario;
use Tests\TestCase;

/**
 * Sicherheits-Header auf allen Antworten (Middleware SecurityHeaders).
 */
class SecurityHeadersTest extends TestCase
{
    public static function pages(): array
    {
        return [
            'Startseite' => ['/', false],
            'Login' => ['/login', false],
            'Admin' => ['/admin', true],
            'API' => ['/api/isFree/test-api-token/3', false],
        ];
    }

    #[DataProvider('pages')]
    public function test_security_headers_are_set(string $uri, bool $admin): void
    {
        $s = BookingScenario::create();
        if ($admin) {
            $this->actingAs($s->user('admin'));
        }

        $this->get($uri)
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()')
            ->assertHeaderMissing('Strict-Transport-Security');
    }

    public function test_hsts_only_in_production_over_https(): void
    {
        BookingScenario::create();

        $this->get('https://localhost/')->assertHeaderMissing('Strict-Transport-Security');

        $this->app['env'] = 'production';
        $this->get('https://localhost/')->assertHeader('Strict-Transport-Security', 'max-age=31536000');
        $this->get('http://localhost/')->assertHeaderMissing('Strict-Transport-Security');
    }
}
