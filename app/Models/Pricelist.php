<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pricelist extends Model
{
    //
    protected $fillable = [
        'name',
        'detail',
        'price',
        'archive_status_id',
        'sort-index'
    ];

    public function archive_status(){
        return $this->belongsTo(ArchiveStatus::class);
    }
}
