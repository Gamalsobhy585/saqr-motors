<?php

namespace App\Policies;

use App\Models\Promotion;
use App\Models\User;

class PromotionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_active && ($user->isAdmin() || $user->isDataEntry());
    }

    public function view(User $user, Promotion $promotion): bool
    {
        return $this->canManage($user, $promotion);
    }

    public function create(User $user): bool
    {
        return $user->is_active && (
            $user->isAdmin() || ($user->isDataEntry() && $user->brands()->exists())
        );
    }

    public function update(User $user, Promotion $promotion): bool
    {
        return $this->canManage($user, $promotion);
    }

    public function delete(User $user, Promotion $promotion): bool
    {
        return $this->canManage($user, $promotion);
    }

    public function deleteAny(User $user): bool
    {
        return false; // No unscoped bulk deletion.
    }

    public function restore(User $user, Promotion $promotion): bool
    {
        return false;
    }

    public function restoreAny(User $user): bool
    {
        return false;
    }

    public function forceDelete(User $user, Promotion $promotion): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }

    public function replicate(User $user, Promotion $promotion): bool
    {
        return false;
    }

    public function reorder(User $user): bool
    {
        return false;
    }

    private function canManage(User $user, Promotion $promotion): bool
    {
        if (! $user->is_active) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        return $user->isDataEntry()
            && $user->brands()->whereKey($promotion->brand_id)->exists();
    }
}
