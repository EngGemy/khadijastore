<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBrand;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements Auditable, HasMedia
{
    use AuditableTrait, BelongsToBrand, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'brand_id', 'category_id', 'name', 'slug', 'mark',
        'short_description', 'description', 'features', 'usage_steps',
        'price', 'compare_price', 'badge', 'video_url',
        'is_active', 'is_featured', 'sort', 'sales_count', 'rating',
        'stock', 'track_stock', 'low_stock_threshold',
    ];

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'usage_steps' => 'array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'rating' => 'decimal:1',
            'track_stock' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(fn (Product $p) => $p->slug ??= Str::slug($p->name).'-'.Str::random(4));
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('gallery');
        $this->addMediaCollection('cover')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')->width(400)->height(400)->nonQueued();
        $this->addMediaConversion('large')->width(1000)->height(1000)->nonQueued();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('sort');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }

    public function recomputeRating(): void
    {
        $avg = $this->approvedReviews()->avg('rating');
        $this->update(['rating' => $avg ? round($avg, 1) : 0]);
    }

    public function getDiscountPercentAttribute(): ?int
    {
        if (! $this->compare_price || $this->compare_price <= $this->price) {
            return null;
        }

        return (int) round(100 - ($this->price / $this->compare_price * 100));
    }

    public function isOutOfStock(): bool
    {
        if ($this->variants->isNotEmpty()) {
            return $this->variants->every(fn ($v) => $v->isOutOfStock());
        }

        return $this->track_stock && $this->stock <= 0;
    }

    public function isLowStock(): bool
    {
        if ($this->variants->isNotEmpty()) {
            return $this->variants->contains(fn ($v) => $v->isLowStock());
        }

        return $this->track_stock && $this->stock > 0 && $this->stock <= $this->low_stock_threshold;
    }

    public function getTotalStockAttribute(): int
    {
        if ($this->variants->isNotEmpty()) {
            return (int) $this->variants->sum('stock');
        }

        return (int) $this->stock;
    }
}
