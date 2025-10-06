<x-layouts.app>
    <div class="grid gap-4 md:grid-cols-3">
        <x-card class="bg-gradient-to-br from-sky-500 to-blue-600 text-white">
            <p class="text-sm uppercase tracking-wide text-blue-100">Total Pengguna</p>
            <p class="mt-2 text-3xl font-bold">{{ number_format($totalUsers) }}</p>
            <p class="text-xs text-blue-100">{{ number_format($totalAccounts) }} rekening aktif</p>
        </x-card>
        <x-card>
            <p class="text-sm font-semibold text-slate-500">Total Omset Bersih</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">Rp {{ number_format($netOmset, 2, ',', '.') }}</p>
            <p class="text-xs text-slate-500">Total setoran: Rp {{ number_format($totalDeposits, 2, ',', '.') }}</p>
        </x-card>
        <x-card>
            <p class="text-sm font-semibold text-slate-500">Total Saldo Terkumpul</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">Rp {{ number_format($totalBalance, 2, ',', '.') }}</p>
            <p class="text-xs text-slate-500">Penarikan: Rp {{ number_format($totalWithdrawals, 2, ',', '.') }}</p>
        </x-card>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <x-card class="lg:col-span-2">
            <div class="flex flex-col gap-2 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-slate-900">Transaksi Terbaru</h2>
                    <p class="text-sm text-slate-500">Pantau status pembayaran dan cetak struk langsung.</p>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-semibold {{ $midtransEnabled ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                        <span class="inline-block h-2 w-2 rounded-full {{ $midtransEnabled ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                        Midtrans {{ $midtransEnabled ? 'aktif' : 'belum dikonfigurasi' }}
                    </span>
                    <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-600">
                        Pending: Rp {{ number_format($pendingDeposits, 2, ',', '.') }}
                    </span>
                </div>
            </div>

            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Waktu</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Pengguna</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Tipe</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Nominal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($latestTransactions as $transaction)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 text-sm text-slate-600">{{ $transaction->created_at->timezone('Asia/Jakarta')->translatedFormat('d F Y, H:i') }}</td>
                                <td class="px-4 py-3 text-sm font-semibold text-slate-700">{{ $transaction->savingsAccount->user->name }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <x-badge :variant="$transaction->type === 'deposit' ? 'success' : 'warning'">
                                        {{ $transaction->type === 'deposit' ? 'Setoran' : 'Penarikan' }}
                                    </x-badge>
                                </td>
                                <td class="px-4 py-3 text-right text-sm font-semibold {{ $transaction->type === 'deposit' ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ ($transaction->type === 'deposit' ? '+' : '-') }}Rp {{ number_format($transaction->amount, 2, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if ($transaction->type === 'deposit')
                                        @if ($transaction->payment_status === \App\Models\Transaction::STATUS_COMPLETED)
                                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Lunas</span>
                                        @elseif ($transaction->payment_status === \App\Models\Transaction::STATUS_PENDING)
                                            <span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">Menunggu</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-700">Gagal</span>
                                        @endif
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">Selesai</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right text-sm">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('transactions.show', $transaction) }}" class="text-blue-600 hover:underline">Detail</a>
                                        <a href="{{ route('transactions.receipt', $transaction) }}" target="_blank" class="text-slate-600 hover:underline">Cetak</a>
                                        @if ($transaction->type === 'deposit' && $transaction->payment_status === \App\Models\Transaction::STATUS_PENDING)
                                            @if ($transaction->payment_url)
                                                <a href="{{ $transaction->payment_url }}" target="_blank" class="text-emerald-600 hover:underline">Bayar</a>
                                            @endif
                                            <form method="POST" action="{{ route('admin.transactions.approve', $transaction) }}">
                                                @csrf
                                                <button type="submit" class="text-xs font-semibold text-emerald-600 hover:underline">Konfirmasi</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-sm text-slate-500">Belum ada transaksi tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        <x-card>
            <h2 class="text-xl font-semibold text-slate-900">Tambah Pengguna</h2>
            <p class="mt-1 text-sm text-slate-500">Buat akun baru untuk nasabah atau admin lainnya.</p>
            <form method="POST" action="{{ route('admin.users.store') }}" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-600">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-600">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-600">Kata Sandi</label>
                    <input type="password" name="password" required class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-600">Konfirmasi Kata Sandi</label>
                    <input type="password" name="password_confirmation" required class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                </div>
                <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-600">
                    <input type="checkbox" name="is_admin" value="1" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    Jadikan admin
                </label>
                <x-button type="submit" class="w-full justify-center">Simpan</x-button>
            </form>
        </x-card>
    </div>

    <x-card>
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-slate-900">Ringkasan Rekening</h2>
                <p class="text-sm text-slate-500">Daftar saldo tertinggi untuk monitoring cepat.</p>
            </div>
            <span class="text-xs font-semibold uppercase tracking-wide text-slate-400">Top {{ $topAccounts->count() }} akun</span>
        </div>
        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Pengguna</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">No. Rekening</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Saldo</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Transaksi Terakhir</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($topAccounts as $account)
                        @php($latest = $account->transactions->first())
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 text-sm font-semibold text-slate-700">{{ $account->user->name }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $account->account_number }}</td>
                            <td class="px-4 py-3 text-right text-sm font-semibold text-slate-900">Rp {{ number_format($account->balance, 2, ',', '.') }}</td>
                            <td class="px-4 py-3 text-sm text-slate-500">
                                @if ($latest)
                                    {{ $latest->created_at->timezone('Asia/Jakarta')->translatedFormat('d F Y, H:i') }}
                                @else
                                    <span class="text-slate-400">Belum ada transaksi</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-sm text-slate-500">Belum ada rekening yang tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</x-layouts.app>
