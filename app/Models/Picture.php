<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Picture extends Model
{
    //
    protected $fillable = [
        'name',
        'photo_id',
        'cropped_photo_id'
    ];

    public function photo(){
        return $this->belongsTo(Photo::class);
    }

    public function cropped_photo(){
        return $this->belongsTo(Photo::class, 'cropped_photo_id', 'id');
    }
}
