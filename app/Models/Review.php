<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBrand;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Review extends Model implements Auditable
{
    use AuditableTrait, BelongsToBrand;

    protected $fillable = [
        'product_id', 'brand_id', 'customer_name', 'rating',
        'comment', 'is_approved', 'governorate',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'is_approved' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saved(function (Review $review) {
            $review->product?->recomputeRating();
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
