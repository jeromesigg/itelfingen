<?php

namespace App\Models;

use App\Enums\ArchiveState;
use Illuminate\Database\Eloquent\Model;

class PricelistPosition extends Model
{
    /** "Frühbezug, Spätabgabe": je eine Pauschale für Early Check-In und Late Check-Out, ohne Rabatt. */
    public const CHECKIN_CHECKOUT_CODE = 220;

    protected $fillable = [
        'name', 'bexio_id', 'bexio_code', 'price', 'archive_status_id', 'show',
    ];

    protected $casts = [
        'archive_status_id' => ArchiveState::class,
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
