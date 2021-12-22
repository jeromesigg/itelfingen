<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    //
    protected $fillable = [
    'name', 'title', 'event_status_id', 'start_date', 'end_date', 
    'firstname', 'group_name', 'email', 'street', 'plz', 'city', 'telephone', 'comment', 'contract', 'contract_status_id', 'contract_intern', 'comment_intern', 'contract_signed',
    'terms','other_adults', 'member_adults', 'other_kids', 'member_kids', 'total_people', 'total_amount', 'total_days', 'bexio_user_id', 'bexio_invoice_id', 'bexio_file_id',
    'user_id', 'cleaning_mail', 'bexio_offer_id', 'discount', 'parking'
    ];

    protected $casts =  [
        'terms' => 'boolean',
        'cleaning_mail' => 'boolean',
    ];
    
    public function event_status(){
        return $this->belongsTo('App\EventStatus');
    }

    public function contract_status(){
        return $this->belongsTo('App\ContractStatus');
    }

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function positions(){
        return $this->hasMany(Position::class);
    }
}
