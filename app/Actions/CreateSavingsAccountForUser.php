<?php

namespace App\Actions;

use App\Models\SavingsAccount;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateSavingsAccountForUser
{
    public function handle(User $user): SavingsAccount
    {
        return DB::transaction(function () use ($user) {
            /** @var SavingsAccount|null $account */
            $account = $user->savingsAccount()->lockForUpdate()->first();

            if ($account) {
                return $account;
            }

            return $user->savingsAccount()->create([
                'balance' => 0,
            ]);
        });
    }
}
