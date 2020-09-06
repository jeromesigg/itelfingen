<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    //
    protected $fillable = [
        'name', 'internal_name', 'default_album'
    ];

    protected $casts = [
        'default_album' => 'boolean',
    ];
}
