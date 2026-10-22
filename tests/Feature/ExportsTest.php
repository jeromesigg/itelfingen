<?php

namespace Tests\Feature;

use App\Exports\ExportBookings;
use App\Exports\ExportNewsletterBookings;
use App\Exports\ExportNewsletterMembers;
use App\Models\Newsletter;
use Maatwebsite\Excel\Facades\Excel;
use Tests\Support\BookingScenario;
use Tests\TestCase;

/**
 * CSV-Exporte für Verwalter und Admin: Buchungen (Auslastung) und Newsletter-Empfänger.
 */
class ExportsTest extends TestCase
{
    public function test_bookings_export_contains_new_and_confirmed_but_not_cancelled(): void
    {
        Excel::fake();
        $s = BookingScenario::create();

        $this->actingAs($s->user('verwalter'))->get('/admin/bookings/export-csv');

        Excel::assertDownloaded('bookings.csv', function (ExportBookings $export) use ($s) {
            $names = $export->collection()->pluck('name');

            return $names->contains($s->event->name)
                && $names->contains($s->confirmedEvent->name)
                && ! $names->contains($s->cancelledEvent->name)
                // Keine Personendaten wie E-Mail oder Telefon
                && ! array_key_exists('email', $export->collection()->first()->toArray())
                && count($export->headings()) === count($export->collection()->first()->toArray());
        });
    }

    public function test_csv_files_are_really_written(): void
    {
        // Ohne Excel::fake(): erzeugt die Dateien wirklich mit PhpSpreadsheet (Wechsel 1.x → 5.x mit Excel 4.0)
        $s = BookingScenario::create();
        $s->event->update(['name' => 'Müller-Grüninger']);

        // Trennzeichen und BOM wie in config/excel.php (Semikolon für Excel in der Schweiz)
        $read = function (string $route) use ($s): array {
            $response = $this->actingAs($s->user('verwalter'))->get($route)->assertOk();
            $csv = preg_replace('/^\xEF\xBB\xBF/', '', file_get_contents($response->baseResponse->getFile()->getPathname()));

            return [$csv, array_map(fn ($line) => str_getcsv($line, config('excel.exports.csv.delimiter'), config('excel.exports.csv.enclosure'), '\\'), array_filter(explode("\n", trim($csv))))];
        };

        [$csv, $rows] = $read('/admin/bookings/export-csv');

        $this->assertSame((new ExportBookings)->headings(), $rows[0]);
        $this->assertCount(count($rows[0]), $rows[1]);
        $this->assertStringContainsString('Müller-Grüninger', $csv);

        foreach (['newsletter.exportBookings' => ExportNewsletterBookings::class, 'newsletter.exportMembers' => ExportNewsletterMembers::class] as $route => $export) {
            $this->assertSame((new $export)->headings(), $read(route($route))[1][0]);
        }
    }

    public function test_newsletter_exports_split_bookings_and_members(): void
    {
        Excel::fake();
        $s = BookingScenario::create();
        $member = Newsletter::create(['name' => 'Mitglied', 'email' => 'mitglied@example.com', 'bookings' => false, 'members' => true]);

        $this->actingAs($s->user('verwalter'))->get('/admin/newsletter/export-bookings');
        $this->actingAs($s->user('verwalter'))->get('/admin/newsletter/export-members');

        Excel::assertDownloaded('newsletter_bookings.csv', fn (ExportNewsletterBookings $export) => $export->collection()->pluck('email')->all() === [$s->newsletter->email]);
        Excel::assertDownloaded('newsletter_members.csv', fn (ExportNewsletterMembers $export) => $export->collection()->pluck('email')->all() === [$member->email]);
    }

    public function test_newsletter_import_adds_invoiced_bookings_once(): void
    {
        $s = BookingScenario::create();

        // "Import" übernimmt bestätigte Buchungen mit Rechnung in die Newsletter-Liste; zweimal ausführen darf nichts verdoppeln.
        $this->actingAs($s->user('verwalter'))->get('/admin/newsletter/import')->assertRedirect('/admin/newsletter');
        $this->actingAs($s->user('verwalter'))->get('/admin/newsletter/import');

        $this->assertSame(1, Newsletter::where('email', $s->confirmedEvent->email)->count());
        $this->assertSame(0, Newsletter::where('email', $s->event->email)->count());
    }
}
