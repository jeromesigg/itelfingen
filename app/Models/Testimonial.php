<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    //
    protected $fillable = [
        'name',
        'function',
        'comment',
        'archive_status_id',
        'sort-index'
    ];

    public function archive_status(){
        return $this->belongsTo(ArchiveStatus::class);
    }
}
