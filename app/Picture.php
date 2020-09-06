<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Picture extends Model
{
    //
    protected $fillable = [
        'name',
        'photo_id',
        'album_id'
    ];

    public function photo(){
        return $this->belongsTo('App\Photo');
    }

    public function album(){
        return $this->belongsTo('App\Album');
    }
}
