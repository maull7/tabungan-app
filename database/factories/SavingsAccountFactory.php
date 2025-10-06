<?php

namespace Database\Factories;

use App\Models\SavingsAccount;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<SavingsAccount>
 */
class SavingsAccountFactory extends Factory
{
    protected $model = SavingsAccount::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'account_number' => 'ACC-'.Str::upper(Str::random(10)),
            'balance' => 0,
        ];
    }
}
