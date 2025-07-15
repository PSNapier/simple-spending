<?php

namespace App\Policies;

use App\Models\Income;
use App\Models\User;

class IncomePolicy
{
    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Income $income)
    {
        return $user->id === $income->user_id;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, Income $income)
    {
        return $user->id === $income->user_id;
    }

    public function delete(User $user, Income $income)
    {
        return $user->id === $income->user_id;
    }

    public function restore(User $user, Income $income)
    {
        return false;
    }

    public function forceDelete(User $user, Income $income)
    {
        return false;
    }
}
