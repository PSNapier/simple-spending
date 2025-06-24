<?php

namespace App\Policies;

use App\Models\Spend;
use App\Models\User;

class SpendPolicy
{
    /**
     * Allow update if the spend belongs to the user.
     */
    public function update(User $user, Spend $spend): bool
    {
        return $spend->user_id === $user->id;
    }

    /**
     * Allow delete if the spend belongs to the user.
     */
    public function delete(User $user, Spend $spend): bool
    {
        return $spend->user_id === $user->id;
    }
}
