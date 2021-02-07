<?php

namespace App;

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
        return $this->belongsTo('App\ArchiveStatus');
    }
}
