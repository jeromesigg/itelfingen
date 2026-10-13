<?php

namespace App\Models;

use App\Enums\ArchiveState;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    //
    protected $fillable = [
        'name',
        'description',
        'faq_chapter_id',
        'photo_id',
        'sort-index',
        'archive_status_id',
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

    public function faq_chapter()
    {
        return $this->belongsTo(FaqChapter::class);
    }
}
