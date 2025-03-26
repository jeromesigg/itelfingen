<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Checkpoint extends Model
{
    //
    protected $fillable = [
        'name', 'sort-index', 'archive_status_id', 'room_id', 'description'
    ];

    
    public function archive_status()
    {
        return $this->belongsTo(ArchiveStatus::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

}

