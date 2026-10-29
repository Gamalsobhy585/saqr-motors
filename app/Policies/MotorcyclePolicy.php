<?php

namespace App\Policies;

use App\Models\Motorcycle;
use App\Models\User;

class MotorcyclePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_active && ($user->isAdmin() || $user->isDataEntry());
    }

    public function view(User $user, Motorcycle $motorcycle): bool
    {
        return $this->canManageBrand($user, $motorcycle->brand_id);
    }

    public function create(User $user): bool
    {
        return $user->is_active && (
            $user->isAdmin() ||
            ($user->isDataEntry() && $user->brands()->exists())
        );
    }

    public function update(User $user, Motorcycle $motorcycle): bool
    {
        return $this->canManageBrand($user, $motorcycle->brand_id);
    }

    public function delete(User $user, Motorcycle $motorcycle): bool
    {
        return $user->is_active && $user->isAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }

    public function restore(User $user, Motorcycle $motorcycle): bool
    {
        return $user->is_active && $user->isAdmin();
    }

    public function restoreAny(User $user): bool
    {
        return false;
    }

    public function forceDelete(User $user, Motorcycle $motorcycle): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }

    public function replicate(User $user, Motorcycle $motorcycle): bool
    {
        return false;
    }

    public function reorder(User $user): bool
    {
        return false;
    }

    private function canManageBrand(User $user, int $brandId): bool
    {
        if (! $user->is_active) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        return $user->isDataEntry()
            && $user->brands()->whereKey($brandId)->exists();
    }
}
