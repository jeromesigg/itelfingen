<?php

namespace Tests\Feature\Security;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Support\BookingScenario;
use Tests\TestCase;

/**
 * API für Kalender, Telefonanlage und Verfügbarkeit (Middleware api_token, Token in der URL).
 */
class ApiTokenTest extends TestCase
{
    public static function endpoints(): array
    {
        return [
            'events' => ['/api/events/%s'],
            'ical' => ['/api/events_ical/%s'],
            'phone number' => ['/api/phone_number/%s/number/0791234567'],
            'is free' => ['/api/isFree/%s/3'],
        ];
    }

    #[DataProvider('endpoints')]
    public function test_valid_token_is_accepted(string $uri): void
    {
        BookingScenario::create();

        $status = $this->get(sprintf($uri, 'test-api-token'))->getStatusCode();

        // 404 ist bei der Telefonnummer die fachliche Antwort "keine Buchung gefunden".
        $this->assertContains($status, [200, 404]);
    }

    #[DataProvider('endpoints')]
    public function test_wrong_token_is_rejected_without_echoing_it(string $uri): void
    {
        BookingScenario::create();

        $response = $this->get(sprintf($uri, 'geraten'));

        $response->assertUnauthorized();
        $response->assertDontSee('geraten');
    }

    #[DataProvider('endpoints')]
    public function test_api_is_closed_without_configured_token(string $uri): void
    {
        BookingScenario::create();
        // Fehlt API_TOKEN in der .env, galt bisher der Standardwert '1234'.
        config(['app.api_token' => null]);

        $this->get(sprintf($uri, '1234'))->assertUnauthorized();
    }

    public function test_api_token_has_no_default_value(): void
    {
        // config/app.php so lesen, wie in Prod ohne API_TOKEN in der .env.
        $original = getenv('API_TOKEN');
        putenv('API_TOKEN');
        unset($_ENV['API_TOKEN'], $_SERVER['API_TOKEN']);

        try {
            $config = require config_path('app.php');
        } finally {
            putenv("API_TOKEN=$original");
            $_ENV['API_TOKEN'] = $_SERVER['API_TOKEN'] = $original;
        }

        $this->assertEmpty($config['api_token']);
    }

    public function test_phone_number_lookup_returns_booking_contact(): void
    {
        $s = BookingScenario::create();
        $s->event->update(['telephone' => '079 123 45 67', 'phonenumber_query' => '0791234567']);

        $this->get('/api/phone_number/test-api-token/number/+41791234567')
            ->assertOk()
            ->assertJson(['name' => $s->event->name]);
    }
}
