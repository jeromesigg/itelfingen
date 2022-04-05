<?php

namespace App\Models;

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
        return $this->belongsTo(ArchiveStatus::class);
    }

    public function photo(){
        return $this->belongsTo(Photo::class);
    }
}
