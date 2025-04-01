<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventRoom extends Model
{
    //
    protected $fillable = [
        'room_id', 'event_id', 'done'
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function event_checkpoints()
    {
        return $this->hasMany(EventCheckpoint::class);
    }    
    
    public function event_checkpoints_open()
    {
        return $this->event_checkpoints()->where('done', false);
    }
}
