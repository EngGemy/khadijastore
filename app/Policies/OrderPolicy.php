<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'brand_admin', 'brand_staff']);
    }

    public function view(User $user, Order $order): bool
    {
        return $user->isSuperAdmin() || $order->brand_id === $user->brand_id;
    }

    // الطلبات تُنشأ من الواجهة الأمامية فقط، لا يدويًا من اللوحة
    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Order $order): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        // brand_admin و brand_staff يحدّثان طلبات براندهم فقط
        return $order->brand_id === $user->brand_id
            && $user->hasAnyRole(['brand_admin', 'brand_staff']);
    }

    public function delete(User $user, Order $order): bool
    {
        // الحذف للسوبر أدمن وأدمن البراند فقط (ليس الموظف)
        return $user->isSuperAdmin()
            || ($order->brand_id === $user->brand_id && $user->hasRole('brand_admin'));
    }
}
