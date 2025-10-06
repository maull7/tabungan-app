<?php

namespace App\Http\Controllers;

use App\Actions\CreateSavingsAccountForUser;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        protected CreateSavingsAccountForUser $createSavingsAccountForUser
    ) {
    }

    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $account = $this->createSavingsAccountForUser->handle($user);

        $recentTransactions = $account->transactions()
            ->latest()
            ->take(5)
            ->get();

        $monthlyTotal = $account->transactions()
            ->whereBetween('created_at', [
                now('Asia/Jakarta')->startOfMonth(),
                now('Asia/Jakarta')->endOfMonth(),
            ])->count();

        return view('dashboard.index', [
            'account' => $account,
            'recentTransactions' => $recentTransactions,
            'monthlyTotal' => $monthlyTotal,
        ]);
    }
}
