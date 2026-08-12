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
        'invalid_items' => 'array',
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

    public static function generateNoAntrean(): string
    {
        $todayPrefix = date('Ymd');
        $lastPermohonan = self::where('no_antrean', 'like', $todayPrefix . '%')
            ->orderBy('no_antrean', 'desc')
            ->first();

        if ($lastPermohonan && $lastPermohonan->no_antrean) {
            $lastSequence = (int) substr($lastPermohonan->no_antrean, -3);
            $nextSequence = $lastSequence + 1;
        } else {
            $nextSequence = 1;
        }

        return $todayPrefix . str_pad((string)$nextSequence, 3, '0', STR_PAD_LEFT);
    }
}
