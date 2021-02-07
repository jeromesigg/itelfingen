<?php

namespace App;

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
        'photo_id'
    ];

    public function archive_status(){
        return $this->belongsTo('App\ArchiveStatus');
    }
    
    public function photo(){
        return $this->belongsTo('App\Photo');
    }
}
