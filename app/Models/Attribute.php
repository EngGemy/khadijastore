<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBrand;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Attribute extends Model implements Auditable
{
    use AuditableTrait, BelongsToBrand;

    protected $fillable = [
        'brand_id',
        'name',
        'code',
        'input_type',
        'sort',
    ];

    protected function casts(): array
    {
        return [
            'input_type' => 'string',
        ];
    }

    public function values(): HasMany
    {
        return $this->hasMany(AttributeValue::class)
            ->orderBy('sort');
    }
}
