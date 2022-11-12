<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Event extends Model
{
    use Notifiable;
    //
    protected $fillable = [
    'name', 'event_status_id', 'start_date', 'end_date',
    'firstname', 'group_name', 'email', 'street', 'plz', 'city', 'telephone', 'comment', 'contract_status_id','comment_intern',
    'terms', 'total_amount', 'total_days', 'bexio_user_id', 'bexio_invoice_id', 'bexio_file_id',
    'user_id', 'cleaning_mail', 'bexio_offer_id', 'discount', 'last_info', 'code', 'feedback_mail', 'external',
    ];

    protected $casts =  [
        'terms' => 'boolean',
        'cleaning_mail' => 'boolean',
        'last_info' => 'boolean',
        'external' => 'boolean',
    ];

    public function event_status(){
        return $this->belongsTo(EventStatus::class);
    }

    public function contract_status(){
        return $this->belongsTo(ContractStatus::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function positions(){
        return $this->hasMany(Position::class);
    }

    public function routeNotificationForSlack($notification)
    {
        return config('slack.endpoint');
    }
}
