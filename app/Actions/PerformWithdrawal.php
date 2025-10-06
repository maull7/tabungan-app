<?php

namespace App\Actions;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class PerformWithdrawal
{
    public function __construct(
        protected CreateSavingsAccountForUser $createSavingsAccountForUser
    ) {}

    public function handle(User $user, float $amount, ?string $note = null): Transaction
    {
        return DB::transaction(function () use ($user, $amount, $note) {
            $this->createSavingsAccountForUser->handle($user);

            $account = $user->savingsAccount()->lockForUpdate()->first();

            $normalizedAmount = number_format($amount, 2, '.', '');

            if (bccomp($account->balance, $normalizedAmount, 2) < 0) {
                throw new InvalidArgumentException('Saldo tidak mencukupi untuk penarikan.');
            }

            $newBalance = bcsub($account->balance, $normalizedAmount, 2);

            $transaction = $account->transactions()->create([
                'type' => Transaction::TYPE_WITHDRAWAL,
                'amount' => $normalizedAmount,
                'running_balance' => $newBalance,
                'receipt_number' => $this->generateReceiptNumber(),
                'note' => $note,
                'payment_status' => Transaction::STATUS_COMPLETED,
            ]);

            $account->update(['balance' => $newBalance]);

            return $transaction;
        });
    }

    protected function generateReceiptNumber(): string
    {
        return 'TX-'.now('Asia/Jakarta')->format('Ymd').'-'.Str::upper(Str::random(6));
    }
}
