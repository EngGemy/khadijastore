<?php

namespace App\Policies;

use App\Models\Brand;
use App\Models\Theme;
use App\Models\User;
use OwenIt\Auditing\Models\Audit;

class SuperAdminPolicy
{
    /** البراندات والثيمات العامة والـ audit والمستخدمون: السوبر أدمن فقط (افتراضيًا) */
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function view(User $user, $model): bool
    {
        return $user->isSuperAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, $model): bool
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, $model): bool
    {
        return $user->isSuperAdmin();
    }
}
