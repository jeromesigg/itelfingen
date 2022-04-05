<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;
    protected $fillable = [
        'amount', 'event_id', 'pricelist_position_id'
    ];

    public function pricelist_position(){
        return $this->belongsTo(PricelistPosition::class);
    }
}
