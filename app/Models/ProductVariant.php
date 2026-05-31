<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class ProductVariant extends Model implements Auditable
{
    use AuditableTrait;

    protected $fillable = [
        'product_id', 'name', 'subtitle', 'price',
        'is_default', 'is_popular', 'sort',
        'stock', 'track_stock', 'low_stock_threshold',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'is_popular' => 'boolean',
            'track_stock' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function isOutOfStock(): bool
    {
        return $this->track_stock && $this->stock <= 0;
    }

    public function isLowStock(): bool
    {
        return $this->track_stock && $this->stock > 0 && $this->stock <= $this->low_stock_threshold;
    }
}
