<?php

namespace App\Models;

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

    public function archive_status()
    {
        return $this->belongsTo(ArchiveStatus::class);
    }

    public function photo()
    {
        return $this->belongsTo(Photo::class);
    }
}
