<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaqChapter extends Model
{
    protected $fillable = [
        'name',
        'sort-index',
        'archive_status_id'
    ];

    public function archive_status(){
        return $this->belongsTo('App\ArchiveStatus');
    }

    public function faqs(){
        return $this->hasMany('App\Faq');
    }
}
