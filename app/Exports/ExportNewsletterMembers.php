<?php

namespace App\Exports;

use App\Models\Newsletter;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExportNewsletterMembers implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Newsletter::where('members', true)->get(['email', 'firstname', 'name', 'members', 'created_at', 'updated_at']);
    }

    public function headings(): array
    {
        return [
            'email',
            'firstname',
            'name',
            'members',
            'created_at',
            'updated_at',
        ];
    }
}
