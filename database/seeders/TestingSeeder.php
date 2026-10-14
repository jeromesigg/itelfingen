<?php

namespace Database\Seeders;

use App\Enums\ArchiveState;
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

        // EventCreatedListener erwartet die Codes 20 (Hausmiete), 50 (Tagesnutzung) und 210 (Parkplätze).
        // Codes 21-200 werden pro Nacht verrechnet, > 200 sind Zusatzkosten (siehe EventOfferCreateListener).
        $positions = [
            ['bexio_code' => 20, 'bexio_id' => 1020, 'name' => 'Hausmiete pro Nacht', 'price' => 300, 'show' => false],
            ['bexio_code' => 30, 'bexio_id' => 1030, 'name' => 'Erwachsene pro Nacht', 'price' => 20, 'show' => true],
            ['bexio_code' => 40, 'bexio_id' => 1040, 'name' => 'Kinder pro Nacht', 'price' => 10, 'show' => true],
            ['bexio_code' => 50, 'bexio_id' => 1050, 'name' => 'Tagesnutzung', 'price' => 150, 'show' => false],
            ['bexio_code' => 210, 'bexio_id' => 1210, 'name' => 'Parkplatz pro Tag', 'price' => 5, 'show' => false],
            ['bexio_code' => 220, 'bexio_id' => 1220, 'name' => 'Endreinigung', 'price' => 150, 'show' => false],
            // Genossenschaftsschein, Rechnung für Bewerbungen (DailyTask)
            ['bexio_code' => 300, 'bexio_id' => 1300, 'name' => 'Genossenschaftsschein', 'price' => 500, 'show' => false],
        ];
        foreach ($positions as $position) {
            PricelistPosition::create($position + ['archive_status_id' => ArchiveState::Aktiv]);
        }
    }
}
