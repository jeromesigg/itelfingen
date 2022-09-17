<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaqChapter extends Model
{
    protected $fillable = [
        'name',
        'sort-index',
        'archive_status_id',
        'photo_id',
        'symbol'
    ];

    public function archive_status(){
        return $this->belongsTo(ArchiveStatus::class);
    }

    public function faqs(){
        return $this->hasMany(Faq::class)->where('archive_status_id', '=', config('status.aktiv'));
    }

    public function photo(){
        return $this->belongsTo(Photo::class);
    }
}
