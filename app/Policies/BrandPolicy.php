<?php

namespace App\Policies;

use App\Models\Brand;
use App\Models\User;

class BrandPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_active
            && ($user->isAdmin() || $user->isDataEntry());
    }

    public function view(User $user, Brand $brand): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->is_active && $user->isAdmin();
    }

    public function update(User $user, Brand $brand): bool
    {
        return $user->is_active && $user->isAdmin();
    }

    public function delete(User $user, Brand $brand): bool
    {
        return $user->is_active && $user->isAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->is_active && $user->isAdmin();
    }

    public function restore(User $user, Brand $brand): bool
    {
        return false;
    }

    public function restoreAny(User $user): bool
    {
        return false;
    }

    public function forceDelete(User $user, Brand $brand): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }

    public function replicate(User $user, Brand $brand): bool
    {
        return $this->create($user);
    }

    public function reorder(User $user): bool
    {
        return $this->create($user);
    }
}
