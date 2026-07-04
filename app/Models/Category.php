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

class Category extends Model implements Auditable
{
    use AuditableTrait, BelongsToBrand;

    protected $fillable = [
        'brand_id',
        'parent_id',
        'name',
        'slug',
        'sort',
        'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function booted(): void
    {
        static::creating(fn (Category $c) => $c->slug ??= Str::slug($c->name));
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

                $department->cover_image = $coverProduct?->getFirstMediaUrl('cover', 'thumb') ?: '';
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
                    'image' => $coverProduct?->getFirstMediaUrl('cover', 'thumb') ?: '',
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
        $result = collect();

        foreach ($categories->where('parent_id', $parentId) as $category) {
            $category->setAttribute('depth', $depth);
            $result->push($category);
            $result = $result->merge(static::flattenForSelect($categories, $category->id, $depth + 1));
        }

        return $result;
    }
}
