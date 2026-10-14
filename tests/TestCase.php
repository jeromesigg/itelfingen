<?php

namespace Tests;

use App\Services\GoogleCalendarService;
use Database\Seeders\TestingSeeder;
use FPDF;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\Support\FakeGoogleCalendar;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    /**
     * Stammdaten (Rollen, Status, Homepage, Admin, Preisliste), keine Demo-Daten.
     */
    protected $seeder = TestingSeeder::class;

    /** Temporäres storage/ dieses Tests */
    protected string $storagePath;

    protected function setUp(): void
    {
        parent::setUp();

        // Tests brauchen kein gebautes Frontend (public/build).
        $this->withoutVite();

        // Kein Test darf echte Dienste (Bexio, Glutz, Slack, Healthchecks) erreichen.
        Http::preventStrayRequests();
        // Der Google-Client läuft nicht über Http und würde mit lokalen Zugangsdaten echte Einträge anlegen.
        $this->app->instance(GoogleCalendarService::class, new FakeGoogleCalendar);

        // Excel-Downloads (maatwebsite/excel) löschen ihre Temp-Datei erst nach dem Senden, was im Test nie passiert.
        config(['excel.temporary_files.local_path' => sys_get_temp_dir().'/laravel-excel-tests']);

        $this->useTemporaryStorage();
    }

    /**
     * Eigenes storage/ pro Test: Parkkarten werden nach storage/app/files/Parkkarten geschrieben, und die
     * PDF-Vorlagen (Infos vor der Buchung, Parkkarte) liegen nicht im Repo. Hier entstehen Platzhalter.
     */
    private function useTemporaryStorage(): void
    {
        $this->storagePath = sys_get_temp_dir().'/itelfingen-storage-'.uniqid();
        File::makeDirectory($this->storagePath.'/app/contracts', 0777, true);
        File::makeDirectory($this->storagePath.'/app/files/Parkkarten', 0777, true);
        $this->app->useStoragePath($this->storagePath);
        config(['filesystems.disks.local.root' => $this->storagePath.'/app']);
        Storage::forgetDisk('local');

        foreach (['app/contracts/Infos_vor_Buchung.pdf', 'app/files/Parkkarte.pdf'] as $template) {
            $pdf = new FPDF;
            $pdf->AddPage();
            $pdf->Output('F', $this->storagePath.'/'.$template);
        }
    }

    protected function tearDown(): void
    {
        // Temp-Dateien der Excel-Downloads und das Test-Storage (siehe setUp) nicht liegen lassen.
        File::deleteDirectory(sys_get_temp_dir().'/laravel-excel-tests');
        File::deleteDirectory($this->storagePath);

        parent::tearDown();
    }

    /**
     * RefreshDatabase leert die Datenbank. Bei gecachter Config (php artisan config:cache)
     * greifen die Werte aus phpunit.xml nicht und es würde die echte Datenbank treffen.
     */
    protected function beforeRefreshingDatabase()
    {
        $connection = config('database.default');
        $database = config("database.connections.$connection.database");

        if ($connection !== 'sqlite' || $database !== ':memory:') {
            throw new RuntimeException(
                "Tests würden gegen '$connection' ($database) laufen statt gegen SQLite in-memory. "
                ."Config-Cache aktiv? 'php artisan config:clear' ausführen."
            );
        }
    }
}
