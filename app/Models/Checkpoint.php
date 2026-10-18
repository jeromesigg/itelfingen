<?php

namespace App\Models;

use App\Enums\ArchiveState;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checkpoint extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'name', 'sort-index', 'archive_status_id', 'room_id', 'description',
    ];

    protected $casts = [
        'archive_status_id' => ArchiveState::class,
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
