<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    //
    protected $fillable = [
        'name', 'email', 'content', 'subject', 'done', 'user_id'
    ];

    protected $casts = [
        'done' => 'boolean',
    ];

    public function user(){
        return $this->belongsTo('App\Models\User');
    }
}
