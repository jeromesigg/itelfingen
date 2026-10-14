<?php

namespace App\Models;

use App\Enums\ArchiveState;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'name', 'sort-index', 'archive_status_id',
    ];

    protected $casts = [
        'archive_status_id' => ArchiveState::class,
    ];

    public function archive_status()
    {
        return $this->belongsTo(ArchiveStatus::class);
    }

    public function checkpoints()
    {
        return $this->hasMany(Checkpoint::class)->where('archive_status_id', '=', ArchiveState::Aktiv)->orderBy('sort-index');
    }
}
