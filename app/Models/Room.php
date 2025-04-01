<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Room extends Model
{
    //
    use HasFactory;
    
    protected $fillable = [
        'name', 'sort-index', 'archive_status_id',
    ];
    
    public function archive_status()
    {
        return $this->belongsTo(ArchiveStatus::class);
    }
    
    public function checkpoints()
    {
        return $this->hasMany(Checkpoint::class)->where('archive_status_id', '=', config('status.aktiv'))->orderBy('sort-index');
    }
}
