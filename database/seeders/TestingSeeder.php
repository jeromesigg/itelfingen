<?php

namespace Database\Seeders;

use App\Models\PricelistPosition;
use Illuminate\Database\Seeder;

/**
 * Stammdaten für automatisierte Tests: dieselben Basisdaten wie in Prod
 * plus die Preislisten-Positionen, auf die der Code mit festen bexio_code zugreift.
 */
class TestingSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(BasisdatenSeeder::class);

        // EventCreatedListener erwartet die Codes 20 (Übernachtung), 50 (Tagesnutzung) und 210 (Reinigung).
        // Codes < 100 sind Personen-/Nutzungspositionen, > 200 Zusatzkosten (siehe EventOfferCreateListener).
        $positions = [
            ['bexio_code' => 20, 'name' => 'Hausmiete pro Nacht', 'price' => 300, 'show' => false],
            ['bexio_code' => 30, 'name' => 'Erwachsene pro Nacht', 'price' => 20, 'show' => true],
            ['bexio_code' => 40, 'name' => 'Kinder pro Nacht', 'price' => 10, 'show' => true],
            ['bexio_code' => 50, 'name' => 'Tagesnutzung', 'price' => 150, 'show' => false],
            ['bexio_code' => 210, 'name' => 'Reinigung', 'price' => 200, 'show' => false],
        ];
        foreach ($positions as $position) {
            PricelistPosition::create($position + ['archive_status_id' => config('status.aktiv')]);
        }
    }
}
