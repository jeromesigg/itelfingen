<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    //
    protected $fillable = [
    'name', 'title', 'event_status_id', 'start_date', 'end_date', 
    'firstname', 'group_name', 'email', 'street', 'plz', 'city', 'telephone', 'comment', 'contract', 'contract_status_id', 'contract_intern', 'comment_intern'
    ];

    // protected $dates = [
    //     'end_date', 'start_date'
    // ];

    public function event_status(){
        return $this->belongsTo('App\EventStatus');
    }

    public function contract_status(){
        return $this->belongsTo('App\ContractStatus');
    }

    // public function getStartDateAttribute($value){
    //     return Carbon::parse($value)->format('Y-m-d');
    // }

    // public function getEndDateAttribute($value){
    //     return Carbon::parse($value)->format('Y-m-d');
    // }
}
