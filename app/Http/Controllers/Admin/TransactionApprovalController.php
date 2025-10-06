<?php

namespace App\Http\Controllers\Admin;

use App\Actions\CompleteDepositPayment;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;

class TransactionApprovalController extends Controller
{
    public function __construct(
        protected CompleteDepositPayment $completeDepositPayment
    ) {
    }

    public function store(Transaction $transaction): RedirectResponse
    {
        if ($transaction->type !== Transaction::TYPE_DEPOSIT) {
            return back()->withErrors(['transaction' => 'Hanya setoran yang bisa dikonfirmasi.']);
        }

        $this->completeDepositPayment->handle($transaction);

        return back()->with('status', 'Pembayaran setoran telah dikonfirmasi.');
    }
}
