<?php

namespace Tests\Feature\Security;

use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Öffentliche Formulare lösen Mails, Slack und Datenbankeinträge aus und sind deshalb pro IP begrenzt
 * (Rate-Limiter "public-forms": 5 pro Minute, 20 pro Stunde).
 */
class PublicFormRateLimitTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
        config(['honeypot.enabled' => false]);
    }

    public static function forms(): array
    {
        return [
            'Buchung' => ['/event/create'],
            'Kontakt' => ['/contacts'],
            'Bewerbung' => ['/applications/store'],
        ];
    }

    #[DataProvider('forms')]
    public function test_sixth_request_per_minute_is_rejected(string $uri): void
    {
        foreach (range(1, 5) as $attempt) {
            $this->assertNotSame(429, $this->post($uri, [])->getStatusCode(), "Versuch $attempt");
        }

        $this->post($uri, [])->assertTooManyRequests();
    }

    public function test_limit_is_per_ip(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.1'])->post('/contacts', []);
        }

        $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.2'])->post('/contacts', [])->assertStatus(302);
    }

    public function test_hourly_limit(): void
    {
        foreach (range(1, 20) as $attempt) {
            $this->travel(61)->seconds();
            $this->assertNotSame(429, $this->post('/contacts', [])->getStatusCode(), "Versuch $attempt");
        }

        $this->travel(61)->seconds();
        $this->post('/contacts', [])->assertTooManyRequests();
    }
}
