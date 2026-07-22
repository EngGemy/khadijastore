<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Theme extends Model implements Auditable
{
    use AuditableTrait;

    protected $fillable = [
        'key', 'name', 'scope', 'brand_id', 'tokens',
        'is_active', 'priority', 'starts_at', 'ends_at',
    ];

    protected function casts(): array
    {
        return [
            'tokens' => 'array',
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /** ثيمات فعّالة الآن (نشطة + ضمن نافذة الجدولة إن وُجدت) */
    public function scopeCurrentlyActive(Builder $query): Builder
    {
        $now = now();

        return $query->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now));
    }

    /** التوكنات الافتراضية — هوية Souqi: كحلي + برتقالي */
    public static function defaultTokens(): array
    {
        return [
            'ink' => '#0B1D36',
            'paper' => '#ffffff',
            'paper2' => '#F5F7FA',
            'accent' => '#E85D04',
            'accentDark' => '#C2410C',
            'brand' => '#E85D04',
            'font' => 'Cairo',
            'strip_text' => 'شحن مجاني داخل القاهرة والجيزة · الدفع عند الاستلام',
            'badge' => null,
        ];
    }
}
