<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class City extends Model
{
    use HasFactory;
    use SearchableTrait;

    protected $fillable = [
        'name', 'plz'
    ];

    protected $searchable = [
        'columns' => [
            'name' => 1,
            'plz' => 1,
        ]
    ];
}
