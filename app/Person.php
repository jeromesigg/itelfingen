<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    //
    
    protected $fillable = [
        'name',
        'function',
        'photo_id',
        'archive_status_id',
        'sort-index'
    ];

    public function archive_status(){
        return $this->belongsTo('App\ArchiveStatus');
    }

    public function photo(){
        return $this->belongsTo('App\Photo');
    }
}
