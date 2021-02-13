<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    //
    protected $fillable = [
    'name', 'title', 'event_status_id', 'start_date', 'end_date', 
    'firstname', 'group_name', 'email', 'street', 'plz', 'city', 'telephone', 'comment', 'contract', 'contract_status_id', 'contract_intern'
    ];

    public function event_status(){
        return $this->belongsTo('App\EventStatus');
    }

    public function contract_status(){
        return $this->belongsTo('App\ContractStatus');
    }
}
