<?php

namespace App\Policies;

use App\Models\FinancialPlan;
use App\Models\User;

class FinancialPlanPolicy
{
    public function viewAny(User $user): bool
    {
        return (bool) $user->is_active && $user->isAdmin();
    }

    public function view(User $user, FinancialPlan $financialPlan): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, FinancialPlan $financialPlan): bool
    {
        return $this->viewAny($user);
    }

    public function delete(User $user, FinancialPlan $financialPlan): bool
    {
        return $this->viewAny($user);
    }

    // Bulk deletion is intentionally not offered.
    public function deleteAny(User $user): bool
    {
        return false;
    }
}
