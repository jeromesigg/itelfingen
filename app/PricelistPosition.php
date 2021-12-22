<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PricelistPosition extends Model
{

    protected $fillable = [
    'name', 'bexio_id', 'bexio_code','price', 'archive_status_id', 'show'
    ];

    protected $casts =  [
        'show' => 'boolean'
    ];

    public function archive_status(){
        return $this->belongsTo('App\ArchiveStatus');
    }
    
}
