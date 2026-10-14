<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BexioToken extends Model
{
    //
    protected $fillable = ['access_token', 'refresh_token', 'expires_at'];

    // Verschlüsselt mit APP_KEY: ein DB-Dump allein gibt keinen Zugriff auf die Buchhaltung.
    // Wird APP_KEY gewechselt, muss Bexio auf der Wartungsseite neu verbunden werden.
    protected $casts = [
        'access_token' => 'encrypted',
        'refresh_token' => 'encrypted',
        'expires_at' => 'datetime',
    ];

    public function isExpired(): bool
    {
        // 60 Sekunden Puffer, um Race Conditions zu vermeiden
        return $this->expires_at->subSeconds(60)->isPast();
    }
}
