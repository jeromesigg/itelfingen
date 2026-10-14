<?php

namespace App\Models;

use App\Enums\ArchiveState;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    //

    protected $fillable = [
        'name',
        'function',
        'photo_id',
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

    public function photo()
    {
        return $this->belongsTo(Photo::class);
    }
}
