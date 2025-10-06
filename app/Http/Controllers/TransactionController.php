<?php

namespace App\Http\Controllers;

use App\Actions\CreateSavingsAccountForUser;
use App\Actions\GenerateReceiptPdf;
use App\Actions\PerformDeposit;
use App\Actions\PerformWithdrawal;
use App\Http\Requests\StoreDepositRequest;
use App\Http\Requests\StoreWithdrawalRequest;
use App\Models\Transaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use InvalidArgumentException;

class TransactionController extends Controller
{
    public function __construct(
        protected PerformDeposit $performDeposit,
        protected PerformWithdrawal $performWithdrawal,
        protected GenerateReceiptPdf $generateReceiptPdf,
        protected CreateSavingsAccountForUser $createSavingsAccountForUser,
    ) {
        $this->middleware('auth');
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

        $transaction = $this->performDeposit->handle($user, $amount, $request->input('note'));

        return redirect()
            ->route('transactions.show', $transaction)
            ->with('status', 'Setoran berhasil disimpan.');
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
