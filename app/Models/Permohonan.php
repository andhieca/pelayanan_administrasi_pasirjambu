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

    public function isNotifRead(): bool
    {
        return !empty($this->notif_read_at) || !empty($this->metadata['notif_read_at']);
    }

    public function markNotifAsRead(): void
    {
        $meta = $this->metadata ?? [];
        $meta['notif_read_at'] = now()->toISOString();
        
        $updates = ['metadata' => $meta];
        try {
            if (\Illuminate\Support\Facades\Schema::hasColumn('permohonans', 'notif_read_at')) {
                $updates['notif_read_at'] = now();
            }
        } catch (\Exception $e) {
            // Ignore schema check error if offline
        }

        $this->update($updates);
    }
}
