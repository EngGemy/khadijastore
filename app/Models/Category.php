<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBrand;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Category extends Model implements Auditable
{
    use AuditableTrait, BelongsToBrand;

    protected $fillable = [
        'brand_id', 'name', 'slug', 'sort', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function booted(): void
    {
        static::creating(fn (Category $c) => $c->slug ??= Str::slug($c->name));
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
