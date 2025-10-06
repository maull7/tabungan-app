<?php

namespace App\Actions;

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class CompleteDepositPayment
{
    public function handle(Transaction $transaction): Transaction
    {
        if ($transaction->type !== Transaction::TYPE_DEPOSIT) {
            throw new InvalidArgumentException('Hanya setoran yang dapat dikonfirmasi.');
        }

        if ($transaction->payment_status === Transaction::STATUS_COMPLETED) {
            return $transaction;
        }

        return DB::transaction(function () use ($transaction) {
            $account = $transaction->savingsAccount()->lockForUpdate()->first();

            $normalizedAmount = number_format((float) $transaction->amount, 2, '.', '');
            $newBalance = bcadd($account->balance, $normalizedAmount, 2);

            $transaction->update([
                'payment_status' => Transaction::STATUS_COMPLETED,
                'running_balance' => $newBalance,
            ]);

            $account->update(['balance' => $newBalance]);

            return $transaction->fresh();
        });
    }
}
