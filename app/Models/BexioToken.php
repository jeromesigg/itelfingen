<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BexioToken extends Model
{
    //
        protected $fillable = ['access_token', 'refresh_token', 'expires_at'];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function isExpired(): bool
    {
        // 60 Sekunden Puffer, um Race Conditions zu vermeiden
        return $this->expires_at->subSeconds(60)->isPast();
    }
}
