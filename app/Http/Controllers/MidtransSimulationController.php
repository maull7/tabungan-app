<?php

namespace App\Http\Controllers;

use App\Actions\CompleteDepositPayment;
use App\Models\Transaction;
use App\Services\MidtransService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MidtransSimulationController extends Controller
{
    public function __construct(
        protected MidtransService $midtransService,
        protected CompleteDepositPayment $completeDepositPayment,
    ) {
    }

    public function show(Transaction $transaction): View
    {
        abort_unless($this->midtransService->isSimulation(), 404);

        Gate::authorize('view', $transaction);

        abort_if($transaction->type !== Transaction::TYPE_DEPOSIT, 404);

        return view('midtrans.simulation', [
            'transaction' => $transaction,
        ]);
    }

    public function store(Request $request, Transaction $transaction): RedirectResponse
    {
        abort_unless($this->midtransService->isSimulation(), 404);

        Gate::authorize('view', $transaction);

        abort_if($transaction->type !== Transaction::TYPE_DEPOSIT, 404);

        if ($transaction->payment_status !== Transaction::STATUS_PENDING) {
            return redirect()
                ->route('transactions.show', $transaction)
                ->with('status', 'Status pembayaran sudah tidak dapat diubah.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:success,failed'],
        ]);

        if ($validated['status'] === 'success') {
            $this->completeDepositPayment->handle($transaction);

            return redirect()
                ->route('transactions.show', $transaction)
                ->with('status', 'Pembayaran simulasi Midtrans berhasil dikonfirmasi.');
        }

        $transaction->update([
            'payment_status' => Transaction::STATUS_FAILED,
        ]);

        return redirect()
            ->route('transactions.show', $transaction)
            ->with('status', 'Pembayaran simulasi Midtrans ditandai gagal.');
    }
}
