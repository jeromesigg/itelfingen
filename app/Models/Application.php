<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Application extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'salutation_id', 'firstname', 'organisation', 'email', 'street', 'plz', 'city', 'telephone', 'comment', 'why',
        'bexio_user_id', 'bexio_invoice_id', 'invoice_send', 'refuse',
    ];

    protected $casts = [
        'invoice_send' => 'boolean',
        'refuse' => 'boolean',
    ];

    public function salutation()
    {
        return $this->belongsTo(Salutation::class);
    }

    public function routeNotificationForSlack($notification)
    {
        return config('slack.endpoint');
    }
}
