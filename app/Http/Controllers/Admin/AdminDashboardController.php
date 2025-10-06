<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SavingsAccount;
use App\Models\Transaction;
use App\Models\User;
use App\Services\MidtransService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function __construct(
        protected MidtransService $midtransService
    ) {
    }

    public function __invoke(Request $request): View
    {
        $totalUsers = User::count();
        $totalAccounts = SavingsAccount::count();
        $totalDeposits = Transaction::where('type', Transaction::TYPE_DEPOSIT)
            ->where('payment_status', Transaction::STATUS_COMPLETED)
            ->sum('amount');
        $totalWithdrawals = Transaction::where('type', Transaction::TYPE_WITHDRAWAL)
            ->sum('amount');
        $pendingDeposits = Transaction::where('type', Transaction::TYPE_DEPOSIT)
            ->where('payment_status', Transaction::STATUS_PENDING)
            ->sum('amount');
        $latestTransactions = Transaction::with(['savingsAccount.user'])
            ->latest()
            ->take(8)
            ->get();

        $totalBalance = SavingsAccount::sum('balance');
        $netOmset = bcsub((string) $totalDeposits, (string) $totalWithdrawals, 2);

        $topAccounts = SavingsAccount::with(['user', 'transactions' => function ($query) {
            $query->latest()->limit(1);
        }])
            ->orderByDesc('balance')
            ->take(6)
            ->get();

        return view('admin.dashboard', [
            'totalUsers' => $totalUsers,
            'totalAccounts' => $totalAccounts,
            'totalDeposits' => $totalDeposits,
            'totalWithdrawals' => $totalWithdrawals,
            'pendingDeposits' => $pendingDeposits,
            'totalBalance' => $totalBalance,
            'netOmset' => $netOmset,
            'latestTransactions' => $latestTransactions,
            'midtransEnabled' => $this->midtransService->isEnabled(),
            'topAccounts' => $topAccounts,
        ]);
    }
}
