<?php

namespace Tests\Unit;

use App\Listeners\EventOfferCreateListener;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Mengenregel der Offerte (bestätigt am 10.10.2026, siehe tests/README.md).
 */
class OfferAmountTest extends TestCase
{
    /**
     * @return array<string, array{int, int, int, int}> [bexio_code, erfasste Menge, Nächte, Menge in der Offerte]
     */
    public static function amounts(): array
    {
        return [
            'Hausmiete fix' => [20, 1, 4, 1],
            'Personen pro Nacht' => [30, 6, 4, 24],
            'Nutzung an der Obergrenze pro Nacht' => [200, 2, 3, 6],
            'Personen ohne Eintrag' => [30, 0, 4, 0],
            'Parkplätze: 3 inbegriffen' => [210, 3, 4, 0],
            'Parkplätze: weniger als inbegriffen' => [210, 1, 4, 0],
            'Parkplätze: 2 zusätzliche pro Nacht' => [210, 5, 4, 8],
            'Zusatzkosten fix (unter Parkplätzen)' => [201, 2, 4, 2],
            'Zusatzkosten fix (über Parkplätzen)' => [220, 1, 4, 1],
            'Genossenschaftsschein fix' => [300, 1, 7, 1],
            'eine Nacht' => [30, 10, 1, 10],
        ];
    }

    #[DataProvider('amounts')]
    public function test_offer_amount(int $code, int $amount, int $nights, int $expected): void
    {
        $this->assertSame($expected, EventOfferCreateListener::offerAmount($code, $amount, $nights));
    }
}
