<?php

namespace App\Exports;

use App\Models\Newsletter;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExportNewsletterBookings implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Newsletter::where('bookings', true)->get(['email', 'firstname', 'name', 'bookings', 'created_at', 'updated_at']);
    }

    public function headings(): array
    {
        return [
            'email',
            'firstname',
            'name',
            'bookings',
            'created_at',
            'updated_at',
        ];
    }
}
