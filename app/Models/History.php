<?php

namespace App\Models;

use App\Enums\ArchiveState;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    //
    protected $fillable = [
        'shorttitle',
        'title',
        'subtitle',
        'description',
        'archive_status_id',
        'sort-index',
        'photo_id',
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
