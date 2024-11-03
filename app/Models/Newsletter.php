<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Newsletter extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'firstname', 'email', 'bookings', 'members'];

    protected $casts = [
        'bookings' => 'boolean',
        'members' => 'boolean',
    ];
}
