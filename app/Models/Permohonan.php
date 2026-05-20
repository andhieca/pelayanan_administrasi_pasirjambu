<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Permohonan extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(LogAntrean::class);
    }

    public function getQueuePositionAttribute()
    {
        if ($this->status !== 'pending') {
            return null;
        }

        return Permohonan::where('status', 'pending')
            ->where('no_antrean', '<=', $this->no_antrean)
            ->count();
    }
}
