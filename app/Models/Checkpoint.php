<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Checkpoint extends Model
{
    //
    use HasFactory;
    
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

