<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBrand;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Category extends Model implements Auditable, HasMedia
{
    use AuditableTrait, BelongsToBrand, InteractsWithMedia;

    protected $fillable = [
        'brand_id',
        'parent_id',
        'name',
        'slug',
        'sort',
        'is_active',
        'promo_headline',
        'promo_cta_text',
        'promo_cta_url',
        'is_promo_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_promo_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(fn (Category $c) => $c->slug ??= Str::slug($c->name));
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('banner')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('card')
            ->fit(Fit::Crop, 800, 420)
            ->nonQueued();

        $this->addMediaConversion('wide')
            ->fit(Fit::Crop, 1200, 420)
            ->nonQueued();
    }

    public function bannerUrl(bool $wide = false): ?string
    {
        $media = $this->getFirstMedia('banner');

        if (! $media) {
            return null;
        }

        $conversion = $wide ? 'wide' : 'card';

        if ($media->hasGeneratedConversion($conversion)) {
            $path = $media->getPath($conversion);
            if (is_string($path) && file_exists($path)) {
                return $media->getUrl($conversion);
            }
        }

        return $media->getUrl();
    }

    public function hasActivePromo(): bool
    {
        if (! $this->is_promo_active) {
            return false;
        }

        return filled($this->promo_headline) || filled($this->bannerUrl());
    }

    public function promoCtaUrl(): string
    {
        if (filled($this->promo_cta_url)) {
            return (string) $this->promo_cta_url;
        }

        $slug = $this->relationLoaded('brand')
            ? $this->brand?->slug
            : $this->brand()->value('slug');

        if (! $slug) {
            return route('products.index');
        }

        return route('brand.shop', ['slug' => $slug, 'dept' => $this->id]);
    }

    public function promoCtaText(): string
    {
        return filled($this->promo_cta_text) ? (string) $this->promo_cta_text : 'تسوّق القسم';
    }

    /**
     * Active department promo banners (root categories), optionally scoped to a store.
     *
     * @return \Illuminate\Support\Collection<int, Category>
     */
    public static function activePromoBanners(?int $brandId = null): \Illuminate\Support\Collection
    {
        $query = static::query()
            ->where('is_promo_active', true)
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->with(['media', 'brand:id,name,slug,mark,logo_path'])
            ->orderBy('sort')
            ->orderBy('name');

        if ($brandId) {
            $query->where('brand_id', $brandId);
        }

        return $query->get()
            ->filter(fn (Category $category) => $category->hasActivePromo())
            ->values();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort')->orderBy('name');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function getDepthAttribute(): int
    {
        $depth = 0;
        $parent = $this->relationLoaded('parent') ? $this->parent : $this->parent()->first();

        while ($parent) {
            $depth++;
            $parent = $parent->relationLoaded('parent') ? $parent->parent : $parent->parent()->first();
        }

        return $depth;
    }

    public function getIndentedNameAttribute(): string
    {
        return str_repeat('— ', $this->depth).$this->name;
    }

    public function getBreadcrumbAttribute(): string
    {
        $parts = [$this->name];
        $parent = $this->parent;

        while ($parent) {
            array_unshift($parts, $parent->name);
            $parent = $parent->parent;
        }

        return implode(' › ', $parts);
    }

    public function wouldCreateCycle(?int $newParentId): bool
    {
        if (! $newParentId || $newParentId === $this->id) {
            return (bool) $newParentId;
        }

        $parent = static::query()->find($newParentId);

        while ($parent) {
            if ($parent->id === $this->id) {
                return true;
            }
            $parent = $parent->parent;
        }

        return false;
    }

    public static function filterDepartmentsForStore(int $brandId): \Illuminate\Support\Collection
    {
        return static::query()
            ->where('brand_id', $brandId)
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->with('media')
            ->where(function ($query) {
                $query->whereHas('products', fn ($q) => $q->where('is_active', true))
                    ->orWhereHas('children', fn ($q) => $q->whereHas('products', fn ($q) => $q->where('is_active', true)));
            })
            ->orderBy('sort')
            ->orderBy('name')
            ->get()
            ->map(function (Category $department) use ($brandId) {
                $childIds = static::query()
                    ->where('brand_id', $brandId)
                    ->where('parent_id', $department->id)
                    ->pluck('id');

                $department->display_count = Product::query()
                    ->where('brand_id', $brandId)
                    ->where('is_active', true)
                    ->where(function ($query) use ($department, $childIds) {
                        $query->where('category_id', $department->id);
                        if ($childIds->isNotEmpty()) {
                            $query->orWhereIn('category_id', $childIds);
                        }
                    })
                    ->count();

                $categoryIds = collect([$department->id])->merge($childIds);
                $coverProduct = Product::query()
                    ->where('brand_id', $brandId)
                    ->where('is_active', true)
                    ->whereIn('category_id', $categoryIds)
                    ->with('media')
                    ->orderByDesc('is_featured')
                    ->orderByDesc('sales_count')
                    ->first();

                $department->cover_image = product_cover_url($coverProduct, true) ?: '';
                $department->banner_image = $department->bannerUrl(true) ?: $department->bannerUrl();
                $department->icon = static::departmentIcon($department->slug);

                return $department;
            })
            ->filter(fn (Category $department) => $department->display_count > 0)
            ->values();
    }

    /**
     * @return \Illuminate\Support\Collection<int, object{name: string, ids: string, parent_ids: string, count: int}>
     */
    public static function filterBrandGroupsForStore(int $brandId): \Illuminate\Support\Collection
    {
        $leaves = static::query()
            ->where('brand_id', $brandId)
            ->whereNotNull('parent_id')
            ->where('is_active', true)
            ->whereHas('products', fn ($query) => $query->where('is_active', true))
            ->with('parent')
            ->withCount(['products' => fn ($query) => $query->where('is_active', true)])
            ->orderBy('sort')
            ->orderBy('name')
            ->get();

        $gradients = [
            ['#0f172a', '#1e3a5f'],
            ['#1a1a2e', '#16213e'],
            ['#1b4332', '#2d6a4f'],
            ['#3d0c02', '#7f1d1d'],
            ['#1e1b4b', '#4338ca'],
            ['#134e4a', '#0d9488'],
        ];

        return $leaves
            ->groupBy('name')
            ->values()
            ->map(function (\Illuminate\Support\Collection $group, int $index) use ($gradients, $brandId) {
                $name = $group->first()->name;
                $categoryIds = $group->pluck('id');
                $coverProduct = Product::query()
                    ->where('brand_id', $brandId)
                    ->whereIn('category_id', $categoryIds)
                    ->where('is_active', true)
                    ->with('media')
                    ->orderByDesc('sales_count')
                    ->first();

                $gradient = $gradients[$index % count($gradients)];

                return (object) [
                    'name' => $name,
                    'slug' => Str::slug($name),
                    'ids' => $categoryIds->implode(','),
                    'parent_ids' => $group->pluck('parent_id')->unique()->implode(','),
                    'count' => (int) $group->sum('products_count'),
                    'image' => product_cover_url($coverProduct, true) ?: '',
                    'departments' => $group->pluck('parent.name')->unique()->filter()->values()->all(),
                    'gradient_from' => $gradient[0],
                    'gradient_to' => $gradient[1],
                ];
            })
            ->sortByDesc('count')
            ->values();
    }

    public static function departmentIcon(string $slug): string
    {
        return match ($slug) {
            'headphones' => '🎧',
            'chargers' => '🔌',
            'computer-accessories' => '💻',
            'phone-accessories' => '📱',
            'skincare' => '✨',
            'makeup' => '💄',
            'oils' => '🌿',
            'spices' => '🌶️',
            default => '📦',
        };
    }

    public static function filterChipsForStore(int $brandId): Collection
    {
        return static::query()
            ->where('brand_id', $brandId)
            ->where('is_active', true)
            ->whereHas('products', fn ($query) => $query->where('is_active', true))
            ->with('parent')
            ->withCount(['products' => fn ($query) => $query->where('is_active', true)])
            ->orderBy('sort')
            ->orderBy('name')
            ->get()
            ->sortBy(fn (Category $category) => [$category->parent_id ? 1 : 0, $category->sort, $category->name])
            ->values();
    }

    /**
     * @return array<int, string>
     */
    public static function hierarchicalOptions(?int $brandId = null, ?Category $exclude = null): array
    {
        $query = static::query()->with('parent')->orderBy('sort')->orderBy('name');

        if ($brandId) {
            $query->where('brand_id', $brandId);
        }

        $categories = $query->get();

        if ($exclude) {
            $categories = $categories->reject(
                fn (Category $category) => $category->id === $exclude->id || $exclude->isAncestorOf($category)
            );
        }

        return static::flattenForSelect($categories)->mapWithKeys(
            fn (Category $category) => [$category->id => $category->indented_name]
        )->all();
    }

    public function isAncestorOf(Category $other): bool
    {
        $parent = $other->parent;

        while ($parent) {
            if ($parent->id === $this->id) {
                return true;
            }
            $parent = $parent->parent;
        }

        return false;
    }

    /**
     * @param  Collection<int, Category>  $categories
     * @return Collection<int, Category>
     */
    public static function flattenForSelect(Collection $categories, ?int $parentId = null, int $depth = 0): Collection
    {
        $result = new Collection;

        foreach ($categories->where('parent_id', $parentId) as $category) {
            $category->setAttribute('depth', $depth);
            $result->push($category);
            $result = $result->merge(static::flattenForSelect($categories, $category->id, $depth + 1));
        }

        return $result;
    }
}
