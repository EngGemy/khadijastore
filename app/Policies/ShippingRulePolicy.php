<?php

namespace App\Policies;

use App\Models\ShippingRule;
use App\Models\User;

class ShippingRulePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'brand_admin', 'brand_staff']);
    }

    public function view(User $user, ShippingRule $rule): bool
    {
        return $user->isSuperAdmin() || $rule->brand_id === $user->brand_id;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'brand_admin']);
    }

    public function update(User $user, ShippingRule $rule): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $rule->brand_id === $user->brand_id
            && $user->hasRole('brand_admin');
    }

    public function delete(User $user, ShippingRule $rule): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $rule->brand_id === $user->brand_id
            && $user->hasRole('brand_admin');
    }
}
