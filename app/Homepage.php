<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Homepage extends Model
{
    //

    protected $fillable = [
        'title', 'subtitle', 'main_photo_id', 
        'background_top_photo_id', 'background_bottom_photo_id', 
        'big_login_photo_id', 'small_login_photo_id','address',
        'phone', 'mail'
    ];

    public function main_photo(){
        return $this->belongsTo('App\Photo', 'main_photo_id', 'id');
    }

    public function top_photo(){
        return $this->belongsTo('App\Photo', 'background_top_photo_id', 'id');
    }

    public function bottom_photo(){
        return $this->belongsTo('App\Photo', 'background_bottom_photo_id', 'id');
    }

    public function big_login_photo(){
        return $this->belongsTo('App\Photo', 'big_login_photo_id', 'id');
    }

    public function small_login_photo(){
        return $this->belongsTo('App\Photo', 'small_login_photo_id', 'id');
    }
}
