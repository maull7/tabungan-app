<?php

namespace App\Providers;

use App\Models\SavingsAccount;
use App\Models\Transaction;
use App\Policies\SavingsAccountPolicy;
use App\Policies\TransactionPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(SavingsAccount::class, SavingsAccountPolicy::class);
        Gate::policy(Transaction::class, TransactionPolicy::class);

        Carbon::setLocale(config('app.locale'));
        setlocale(LC_TIME, 'id_ID.UTF-8');
    }
}
