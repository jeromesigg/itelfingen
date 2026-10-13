<?php

namespace App\Models;

use App\Enums\ArchiveState;
use Illuminate\Database\Eloquent\Model;

class Pricelist extends Model
{
    //
    protected $fillable = [
        'name',
        'detail',
        'price',
        'archive_status_id',
        'sort-index',
    ];

    protected $casts = [
        'archive_status_id' => ArchiveState::class,
    ];

    public function archive_status()
    {
        return $this->belongsTo(ArchiveStatus::class);
    }
}
