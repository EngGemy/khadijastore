<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class ShippingRule extends Model implements Auditable
{
    use AuditableTrait;

    protected $fillable = [
        'brand_id', 'name', 'type', 'value', 'scope',
        'governorate_ids', 'min_order_total', 'priority',
        'is_active', 'starts_at', 'ends_at',
    ];

    protected function casts(): array
    {
        return [
            'governorate_ids' => 'array',
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'priority' => 'integer',
            'min_order_total' => 'integer',
            'value' => 'integer',
        ];
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function scopeCurrentlyActive(Builder $query): Builder
    {
        $now = now();

        return $query->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now));
    }

    public function appliesToGovernorate(string $governorateName): bool
    {
        if ($this->scope === 'all') {
            return true;
        }

        $ids = $this->governorate_ids ?? [];
        if (empty($ids)) {
            return true;
        }

        // governorate_ids may contain names or IDs
        return in_array($governorateName, $ids, true);
    }
}
