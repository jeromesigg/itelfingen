<?php

namespace App\Exports;

use App\Models\Event;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportBookings implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Event::where('event_status_id', '<=', config('status.event_bestaetigt'))->get(['start_date', 'end_date', 'event_status_id', 'total_days', 'total_amount', 'total_people', 'external', 'early_checkin', 'late_checkout']);
    }

    public function headings(): array
    {
        return [
            'Datum Start',
            'Datum Ende',
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
