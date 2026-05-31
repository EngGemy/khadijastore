<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'brand_admin', 'brand_staff']);
    }

    public function view(User $user, Review $review): bool
    {
        return $user->isSuperAdmin() || $review->brand_id === $user->brand_id;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Review $review): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $review->brand_id === $user->brand_id
            && $user->hasAnyRole(['brand_admin', 'brand_staff']);
    }

    public function delete(User $user, Review $review): bool
    {
        return $user->isSuperAdmin()
            || ($review->brand_id === $user->brand_id && $user->hasRole('brand_admin'));
    }
}
