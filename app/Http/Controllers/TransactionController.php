<?php

namespace App\Http\Controllers;

use App\Actions\CreateSavingsAccountForUser;
use App\Actions\GenerateReceiptPdf;
use App\Actions\PerformDeposit;
use App\Actions\PerformWithdrawal;
use App\Http\Requests\StoreDepositRequest;
use App\Http\Requests\StoreWithdrawalRequest;
use App\Models\Transaction;
use App\Services\MidtransService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use InvalidArgumentException;
use Throwable;

class TransactionController extends Controller
{
    public function __construct(
        protected PerformDeposit $performDeposit,
        protected PerformWithdrawal $performWithdrawal,
        protected GenerateReceiptPdf $generateReceiptPdf,
        protected CreateSavingsAccountForUser $createSavingsAccountForUser,
        protected MidtransService $midtransService,
    ) {
    }

    public function index(Request $request): View
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        $account = $user->savingsAccount ?: $this->createSavingsAccountForUser->handle($user);

        $query = $account->transactions()->latest();

        $type = $request->filled('type') ? $request->string('type')->toString() : null;

        if ($type) {
            $query->where('type', $type);
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->date('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->date('to'));
        }

        /** @var LengthAwarePaginator $transactions */
        $transactions = $query->paginate(10)->withQueryString();

        return view('transactions.index', [
            'account' => $account,
            'transactions' => $transactions,
            'filters' => [
                'type' => $type,
                'from' => $request->input('from'),
                'to' => $request->input('to'),
            ],
        ]);
    }

    public function create(Request $request): View
    {
        $this->createSavingsAccountForUser->handle($request->user());

        return view('transactions.create');
    }

    public function store(StoreDepositRequest $request): RedirectResponse
    {
        $user = $request->user();
        $amount = (float) $request->input('amount');

        $paymentStatus = $this->midtransService->isEnabled()
            ? Transaction::STATUS_PENDING
            : Transaction::STATUS_COMPLETED;

        $transaction = $this->performDeposit->handle(
            $user,
            $amount,
            $request->input('note'),
            $paymentStatus,
        );

        if ($paymentStatus === Transaction::STATUS_PENDING) {
            try {
                $payment = $this->midtransService->createSnapTransaction($transaction);

                $transaction->forceFill([
                    'payment_provider' => 'midtrans',
                    'payment_reference' => $payment['order_id'] ?? null,
                    'payment_token' => $payment['token'] ?? null,
                    'payment_url' => $payment['redirect_url'] ?? null,
                ])->save();
            } catch (Throwable $exception) {
                $transaction->forceFill([
                    'payment_status' => Transaction::STATUS_FAILED,
                ])->save();

                return redirect()
                    ->route('transactions.show', $transaction)
                    ->withErrors([
                        'midtrans' => 'Gagal membuat permintaan pembayaran Midtrans: '.$exception->getMessage(),
                    ]);
            }
        }

        return redirect()
            ->route('transactions.show', $transaction)
            ->with('status', $paymentStatus === Transaction::STATUS_PENDING
                ? 'Setoran berhasil dibuat. Silakan selesaikan pembayaran melalui Midtrans.'
                : 'Setoran berhasil disimpan.'
            );
    }

    public function storeWithdrawal(StoreWithdrawalRequest $request): RedirectResponse
    {
        $user = $request->user();
        $amount = (float) $request->input('amount');

        try {
            $transaction = $this->performWithdrawal->handle($user, $amount, $request->input('note'));
        } catch (InvalidArgumentException $exception) {
            return back()->withErrors(['amount' => $exception->getMessage()])->withInput();
        }

        return redirect()
            ->route('transactions.show', $transaction)
            ->with('status', 'Penarikan berhasil disimpan.');
    }

    public function show(Transaction $transaction): View
    {
        Gate::authorize('view', $transaction);

        return view('transactions.show', [
            'transaction' => $transaction,
            'account' => $transaction->savingsAccount,
        ]);
    }

    public function receipt(Transaction $transaction)
    {
        Gate::authorize('view', $transaction);

        return $this->generateReceiptPdf->handle($transaction);
    }
}
