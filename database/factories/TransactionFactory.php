<?php

namespace Database\Factories;

use App\Models\SavingsAccount;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        $type = $this->faker->randomElement([
            Transaction::TYPE_DEPOSIT,
            Transaction::TYPE_WITHDRAWAL,
        ]);

        $amount = $this->faker->numberBetween(10_000, 500_000);

        return [
            'savings_account_id' => SavingsAccount::factory(),
            'type' => $type,
            'amount' => $amount,
            'running_balance' => $type === Transaction::TYPE_WITHDRAWAL
                ? $this->faker->numberBetween(0, $amount)
                : $amount,
            'receipt_number' => 'TX-'.$this->faker->date('Ymd').'-'.Str::upper(Str::random(6)),
            'note' => $this->faker->sentence(),
        ];
    }
}
