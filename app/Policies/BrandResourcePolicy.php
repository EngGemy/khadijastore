<?php

namespace App\Policies;

use App\Models\User;

/**
 * Policy موحّد للموارد التابعة للبراند (Product, Category).
 * brand_admin يدير موارد برانده، brand_staff يقرأ فقط، super_admin يدير الكل.
 */
class BrandResourcePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'brand_admin', 'brand_staff']);
    }

    public function view(User $user, $model): bool
    {
        return $user->isSuperAdmin() || $model->brand_id === $user->brand_id;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'brand_admin']);
    }

    public function update(User $user, $model): bool
    {
        return $user->isSuperAdmin()
            || ($model->brand_id === $user->brand_id && $user->hasRole('brand_admin'));
    }

    public function delete(User $user, $model): bool
    {
        return $this->update($user, $model);
    }
}
