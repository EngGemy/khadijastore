<?php

namespace App\Models\Concerns;

use App\Models\Brand;
use App\Models\Scopes\BrandScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * يضيف عزل البراند تلقائيًا:
 * - global scope للقراءة
 * - تعبئة brand_id تلقائيًا عند الإنشاء من المستخدم الحالي
 */
trait BelongsToBrand
{
    public static function bootBelongsToBrand(): void
    {
        static::addGlobalScope(new BrandScope);

        static::creating(function ($model) {
            if (! $model->brand_id && ($user = Auth::user()) && $user->brand_id) {
                $model->brand_id = $user->brand_id;
            }
        });
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /** استعلام يتخطّى العزل (للسوبر أدمن أو التقارير المجمّعة) */
    public function scopeAcrossBrands($query)
    {
        return $query->withoutGlobalScope(BrandScope::class);
    }
}
