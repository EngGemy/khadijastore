<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

/**
 * يقيّد الاستعلامات تلقائيًا ببراند المستخدم الحالي.
 * السوبر أدمن (بلا brand_id) يرى كل البراندات.
 */
class BrandScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $user = Auth::user();

        // لا قيود في الـ CLI/الـ seeders/الطلبات العامة (الواجهة الأمامية)
        if (! $user) {
            return;
        }

        // السوبر أدمن يرى الكل
        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return;
        }

        // باقي المستخدمين: براندهم فقط
        if ($user->brand_id) {
            $builder->where($model->getTable().'.brand_id', $user->brand_id);
        }
    }
}
