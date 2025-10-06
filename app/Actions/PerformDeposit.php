<?php

namespace App\Actions;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PerformDeposit
{
    public function __construct(
        protected CreateSavingsAccountForUser $createSavingsAccountForUser
    ) {}

    public function handle(
        User $user,
        float $amount,
        ?string $note = null,
        string $paymentStatus = Transaction::STATUS_COMPLETED
    ): Transaction
    {
        return DB::transaction(function () use ($user, $amount, $note, $paymentStatus) {
            $this->createSavingsAccountForUser->handle($user);

            $account = $user->savingsAccount()->lockForUpdate()->first();

            $normalizedAmount = number_format($amount, 2, '.', '');
            $newBalance = bcadd($account->balance, $normalizedAmount, 2);

            $transaction = $account->transactions()->create([
                'type' => Transaction::TYPE_DEPOSIT,
                'amount' => $normalizedAmount,
                'running_balance' => $newBalance,
                'receipt_number' => $this->generateReceiptNumber(),
                'note' => $note,
                'payment_status' => $paymentStatus,
            ]);

            if ($paymentStatus === Transaction::STATUS_COMPLETED) {
                // Update saldo di akhir agar konsisten
                $account->update(['balance' => $newBalance]);
            }

            return $transaction;
        });
    }

    protected function generateReceiptNumber(): string
    {
        return 'TX-'.now('Asia/Jakarta')->format('Ymd').'-'.Str::upper(Str::random(6));
    }
}
