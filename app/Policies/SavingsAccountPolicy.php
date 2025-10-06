<?php

namespace App\Policies;

use App\Models\SavingsAccount;
use App\Models\User;

class SavingsAccountPolicy
{
    public function view(User $user, SavingsAccount $account): bool
    {
        return $account->user_id === $user->id;
    }

    public function update(User $user, SavingsAccount $account): bool
    {
        return $this->view($user, $account);
    }
}
