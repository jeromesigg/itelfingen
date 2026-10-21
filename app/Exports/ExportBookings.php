<?php

namespace App\Exports;

use App\Enums\BookingState;
use App\Models\Event;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportBookings implements FromCollection, WithHeadings
{
    public function collection(): Collection
    {
        return Event::where('event_status_id', '<=', BookingState::Bestaetigt->value)->get(['start_date', 'end_date', 'name', 'firstname', 'group_name', 'event_status_id', 'total_days', 'total_amount', 'total_people', 'external', 'early_checkin', 'late_checkout']);
    }

    public function headings(): array
    {
        return [
            'Datum Start',
            'Datum Ende',
            'Name',
            'Vorname',
            'Gruppe',
            'Status',
            'Total Tage',
            'Total Betrag',
            'Total Personen',
            'Externe Buchung',
            'Early CheckIn',
            'Late CheckOut',
        ];
    }
}
