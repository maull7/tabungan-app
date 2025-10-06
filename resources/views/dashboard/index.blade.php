<x-layouts.app>
    <div class="grid gap-6 md:grid-cols-2">
        <x-card class="bg-gradient-to-br from-blue-600 to-blue-500 text-white">
            <div class="flex flex-col gap-2">
                <span class="text-sm uppercase tracking-wide text-blue-100">Saldo Tabungan</span>
                <span class="text-4xl font-bold">{{ 'Rp '.number_format($account->balance, 2, ',', '.') }}</span>
                <span class="text-sm text-blue-100">No. Rekening: {{ $account->account_number }}</span>
            </div>
        </x-card>
        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-slate-500">Transaksi bulan ini</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">{{ $monthlyTotal }}</p>
                </div>
                <x-button as="a" href="{{ route('transactions.create') }}">Setor Dana</x-button>
            </div>
        </x-card>
    </div>

    <x-card>
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-900">Transaksi Terbaru</h2>
            <a href="{{ route('transactions.index') }}" class="text-sm font-semibold text-blue-600 hover:underline">Lihat semua</a>
        </div>
        @if ($recentTransactions->isEmpty())
            <div class="flex flex-col items-center justify-center gap-3 py-10 text-center text-slate-500">
                <svg class="h-12 w-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5h18M3 12h18M3 19h18" />
                </svg>
                <p>Belum ada transaksi tercatat.</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach ($recentTransactions as $transaction)
                    <div class="flex items-center justify-between rounded-xl border border-slate-100 px-4 py-3">
                        <div>
                            <p class="font-semibold text-slate-900">
                                {{ $transaction->type === \App\Models\Transaction::TYPE_DEPOSIT ? 'Setoran' : 'Penarikan' }}
                            </p>
                            <p class="text-sm text-slate-500">{{ $transaction->created_at->timezone('Asia/Jakarta')->translatedFormat('d F Y, H:i') }}</p>
                            @if ($transaction->type === \App\Models\Transaction::TYPE_DEPOSIT)
                                <p class="mt-1 text-xs font-semibold">
                                    @if ($transaction->payment_status === \App\Models\Transaction::STATUS_COMPLETED)
                                        <span class="text-emerald-600">Lunas</span>
                                    @elseif ($transaction->payment_status === \App\Models\Transaction::STATUS_PENDING)
                                        <span class="text-amber-600">Menunggu Pembayaran</span>
                                    @else
                                        <span class="text-rose-600">Gagal</span>
                                    @endif
                                </p>
                            @endif
                        </div>
                        <div class="text-right">
                            <p class="font-semibold {{ $transaction->type === \App\Models\Transaction::TYPE_DEPOSIT ? 'text-green-600' : 'text-red-600' }}">
                                {{ ($transaction->type === \App\Models\Transaction::TYPE_DEPOSIT ? '+' : '-') }}{{ 'Rp '.number_format($transaction->amount, 2, ',', '.') }}
                            </p>
                            <p class="text-xs text-slate-400">Saldo: {{ 'Rp '.number_format($transaction->running_balance, 2, ',', '.') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-card>
</x-layouts.app>
