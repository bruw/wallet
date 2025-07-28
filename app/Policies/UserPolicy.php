<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine if the given user can access as an admin.
     */
    public function accessAsAdmin(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if the given user can access as a consumer.
     */
    public function accessAsConsumer(User $user): bool
    {
        return $user->isConsumer();
    }
}
