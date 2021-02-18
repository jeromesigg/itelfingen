<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Picture extends Model
{
    //
    protected $fillable = [
        'name',
        'photo_id',
        'album_id',
        'cropped_photo_id'
    ];

    public function photo(){
        return $this->belongsTo('App\Photo');
    }

    public function cropped_photo(){
        return $this->belongsTo('App\Photo', 'cropped_photo_id', 'id');
    }


    public function album(){
        return $this->belongsTo('App\Album');
    }
}
