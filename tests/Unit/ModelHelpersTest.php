<?php

namespace Tests\Unit;

use App\Models\BexioToken;
use App\Models\Event;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\UnitTestCase;

/**
 * Kleine Hilfsmethoden an Models, ohne Datenbank.
 */
class ModelHelpersTest extends UnitTestCase
{
    public static function bookingNumbers(): array
    {
        return [
            'einstellig' => [7, '00007'],
            'fünfstellig' => [12345, '12345'],
            'sechsstellig' => [123456, '123456'],
        ];
    }

    #[DataProvider('bookingNumbers')]
    public function test_booking_number_has_five_digits(int $id, string $number): void
    {
        $this->assertSame($number, (new Event)->forceFill(['id' => $id])->number());
    }

    public static function tokenAges(): array
    {
        return [
            'noch eine Stunde gültig' => [3600, false],
            'noch 61 Sekunden gültig' => [61, false],
            'noch 59 Sekunden gültig (Puffer)' => [59, true],
            'abgelaufen' => [-10, true],
        ];
    }

    #[DataProvider('tokenAges')]
    public function test_bexio_token_expires_with_one_minute_buffer(int $secondsLeft, bool $expired): void
    {
        $this->freezeTime();
        $token = (new BexioToken)->forceFill(['expires_at' => now()->addSeconds($secondsLeft)]);

        $this->assertSame($expired, $token->isExpired());
    }
}
