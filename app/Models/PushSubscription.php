<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PushSubscription extends Model
{
    protected $fillable = [
        'endpoint_hash',
        'endpoint',
        'public_key',
        'auth_token',
        'content_encoding',
        'customer_phone',
        'user_id',
        'brand_id',
        'last_seen_at',
        'user_agent',
    ];

    public static function hashEndpoint(string $endpoint): string
    {
        return hash('sha256', $endpoint);
    }

    protected function casts(): array
    {
        return [
            'last_seen_at' => 'datetime',
        ];
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function normalizePhone(?string $phone): ?string
    {
        if ($phone === null || $phone === '') {
            return null;
        }

        $digits = preg_replace('/\D/', '', $phone) ?: '';

        return $digits !== '' ? $digits : null;
    }
}
