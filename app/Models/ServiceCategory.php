<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBrand;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class ServiceCategory extends Model implements Auditable
{
    use AuditableTrait, BelongsToBrand;

    protected $fillable = [
        'brand_id', 'type', 'name', 'slug', 'name_en', 'sort', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort'      => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (ServiceCategory $cat) {
            $cat->slug ??= Str::slug($cat->name);
        });
    }

    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class);
    }
}
