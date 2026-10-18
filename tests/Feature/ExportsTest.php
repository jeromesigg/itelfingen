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
