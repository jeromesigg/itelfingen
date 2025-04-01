<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventCheckpoint extends Model
{
    //
    protected $fillable = [
        'event_rooms_id', 'checkpoint_id', 'done'
    ];
    
    public function event_room()
    {
        return $this->belongsTo(EventRoom::class);
    }

    public function checkpoint()
    {
        return $this->belongsTo(Checkpoint::class);
    }
}
