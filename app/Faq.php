<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
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
        'archive_status_id'
    ];

    public function archive_status(){
        return $this->belongsTo('App\ArchiveStatus');
    }
    
    public function photo(){
        return $this->belongsTo('App\Photo');
    }

    public function faq_chapter(){
        return $this->belongsTo('App\FaqChapter');
    }
}
