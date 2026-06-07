<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBrand;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Listing extends Model implements Auditable, HasMedia
{
    use AuditableTrait, BelongsToBrand, InteractsWithMedia, SoftDeletes;

    // أنواع الدليل — زيادة نوع جديد = إضافة ثابت فقط بلا migration
    const TYPE_DOCTOR  = 'doctor';
    const TYPE_NURSERY = 'nursery';

    public static function types(): array
    {
        return [
            self::TYPE_DOCTOR  => 'طبيب',
            self::TYPE_NURSERY => 'حضانة',
        ];
    }

    protected $fillable = [
        'brand_id', 'service_category_id', 'type',
        'name', 'slug', 'name_en',
        'summary', 'summary_en', 'description', 'description_en',
        'phone', 'whatsapp', 'email', 'address', 'address_en',
        'governorate', 'map_url', 'data',
        'rating', 'views', 'is_active', 'is_featured', 'sort',
        'meta_title', 'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'data'        => 'array',
            'is_active'   => 'boolean',
            'is_featured' => 'boolean',
            'rating'      => 'decimal:1',
            'views'       => 'integer',
            'sort'        => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Listing $listing) {
            $listing->slug ??= Str::slug($listing->name) . '-' . Str::random(4);
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')->singleFile();
        $this->addMediaCollection('gallery');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')->width(400)->height(400)->nonQueued();
        $this->addMediaConversion('large')->width(1000)->height(1000)->nonQueued();
    }

    public function serviceCategory(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getWhatsappUrlAttribute(): ?string
    {
        if (! $this->whatsapp) {
            return null;
        }
        $digits = preg_replace('/\D/', '', $this->whatsapp);

        return 'https://wa.me/' . $digits . '?text=' . rawurlencode('مرحباً، وجدتكم عبر الدليل');
    }

    public function getAgeRangeTextAttribute(): string
    {
        $data    = $this->data ?? [];
        $fromMo  = (int) ($data['age_from_months'] ?? 0);
        $toMo    = (int) ($data['age_to_months'] ?? 0);

        $fmt = function (int $months): string {
            if ($months < 12) {
                return $months . ' شهر';
            }
            $years = intdiv($months, 12);
            $rem   = $months % 12;

            return $years . ' سنة' . ($rem ? ' و' . $rem . ' شهر' : '');
        };

        if (! $fromMo && ! $toMo) {
            return '';
        }

        return 'من ' . $fmt($fromMo) . ' إلى ' . $fmt($toMo);
    }

    public function getFeesRangeTextAttribute(): string
    {
        $data = $this->data ?? [];
        $from = $data['monthly_fee_from'] ?? null;
        $to   = $data['monthly_fee_to'] ?? null;

        if (! $from && ! $to) {
            return '';
        }
        if ($from && $to) {
            return number_format($from) . ' – ' . number_format($to) . ' ج.م / شهر';
        }

        return 'من ' . number_format($from ?: $to) . ' ج.م / شهر';
    }

    public function incrementViews(): void
    {
        $this->increment('views');
    }
}
