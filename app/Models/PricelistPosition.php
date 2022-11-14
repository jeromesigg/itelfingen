<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricelistPosition extends Model
{
    protected $fillable = [
        'name', 'bexio_id', 'bexio_code', 'price', 'archive_status_id', 'show',
    ];

    protected $casts = [
        'show' => 'boolean',
    ];

    protected static function booted()
    {
        static::addGlobalScope(fn ($query) => $query->orderBy('bexio_code'));
    }

    public function archive_status()
    {
        return $this->belongsTo(ArchiveStatus::class);
    }
}
