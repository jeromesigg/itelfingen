<?php

namespace App\Exports;

use App\Models\Newsletter;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportNewsletterBookings implements FromCollection, WithHeadings
{
    public function collection(): Collection
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
