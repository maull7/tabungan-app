<x-layouts.app>
    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-900">Riwayat Transaksi</h2>
            <p class="text-sm text-slate-500">Saldo saat ini: <span class="font-semibold">{{ 'Rp '.number_format($account->balance, 2, ',', '.') }}</span></p>
        </div>
        <div>
            <a href="{{ route('transactions.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 font-semibold text-white shadow hover:bg-blue-700">
                + Transaksi Baru
            </a>
        </div>
    </div>

    <x-card>
        <form method="GET" action="{{ route('transactions.index') }}" class="grid gap-4 md:grid-cols-4">
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-600">Tipe</label>
                <select name="type" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                    <option value="">Semua</option>
                    <option value="deposit" @selected(($filters['type'] ?? '') === 'deposit')>Setoran</option>
                    <option value="withdrawal" @selected(($filters['type'] ?? '') === 'withdrawal')>Penarikan</option>
                </select>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-600">Dari</label>
                <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-600">Sampai</label>
                <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
            </div>
            <div class="flex items-end gap-2">
                <x-button type="submit" class="flex-1 justify-center">Terapkan</x-button>
                <x-button href="{{ route('transactions.index') }}" variant="secondary">Reset</x-button>
            </div>
        </form>
    </x-card>

    <x-card>
        @if ($transactions->isEmpty())
            <div class="flex flex-col items-center justify-center gap-3 py-10 text-center text-slate-500">
                <svg class="h-12 w-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5h18M3 12h18M3 19h18" />
                </svg>
                <p>Tidak ada transaksi pada rentang ini.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-600">Tanggal</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-600">Tipe</th>
                            <th class="px-4 py-3 text-right text-sm font-semibold text-slate-600">Nominal</th>
                            <th class="px-4 py-3 text-right text-sm font-semibold text-slate-600">Saldo Berjalan</th>
                            <th class="px-4 py-3 text-right text-sm font-semibold text-slate-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($transactions as $transaction)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 text-sm text-slate-600">{{ $transaction->created_at->timezone('Asia/Jakarta')->translatedFormat('d F Y, H:i') }}</td>
                                <td class="px-4 py-3 text-sm text-slate-600">
                                    <x-badge :variant="$transaction->type === 'deposit' ? 'success' : 'warning'">
                                        {{ $transaction->type === 'deposit' ? 'Setoran' : 'Penarikan' }}
                                    </x-badge>
                                </td>
                                <td class="px-4 py-3 text-right text-sm font-semibold {{ $transaction->type === 'deposit' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ ($transaction->type === 'deposit' ? '+' : '-') }}{{ 'Rp '.number_format($transaction->amount, 2, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right text-sm text-slate-600">{{ 'Rp '.number_format($transaction->running_balance, 2, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right text-sm">
                                    <a href="{{ route('transactions.show', $transaction) }}" class="font-semibold text-blue-600 hover:underline">Detail</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $transactions->links() }}
            </div>
        @endif
    </x-card>
</x-layouts.app>
