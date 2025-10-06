<x-layouts.app>
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-900">Detail Transaksi</h2>
            <p class="text-sm text-slate-500">Nomor struk: {{ $transaction->receipt_number }}</p>
        </div>
        <div class="flex gap-2">
            <x-button href="{{ route('transactions.receipt', $transaction) }}" target="_blank">Cetak Struk</x-button>
            <x-button href="{{ route('transactions.index') }}" variant="secondary">Kembali</x-button>
        </div>
    </div>

    <x-card>
        <dl class="grid gap-4 md:grid-cols-2">
            <div>
                <dt class="text-sm font-medium text-slate-500">Jenis Transaksi</dt>
                <dd class="mt-1 text-lg font-semibold text-slate-900">
                    {{ $transaction->type === 'deposit' ? 'Setoran' : 'Penarikan' }}
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-slate-500">Tanggal</dt>
                <dd class="mt-1 text-lg font-semibold text-slate-900">
                    {{ $transaction->created_at->timezone('Asia/Jakarta')->translatedFormat('d F Y, H:i') }} WIB
                </dd>
            </div>
            @if ($transaction->type === 'deposit')
                <div>
                    <dt class="text-sm font-medium text-slate-500">Status Pembayaran</dt>
                    <dd class="mt-1 text-lg font-semibold">
                        @if ($transaction->payment_status === \App\Models\Transaction::STATUS_COMPLETED)
                            <span class="text-emerald-600">Lunas</span>
                        @elseif ($transaction->payment_status === \App\Models\Transaction::STATUS_PENDING)
                            <span class="text-amber-600">Menunggu Pembayaran</span>
                            <p class="mt-1 text-sm font-normal text-amber-600">Saldo akan bertambah setelah pembayaran dikonfirmasi.</p>
                        @else
                            <span class="text-rose-600">Gagal</span>
                        @endif
                    </dd>
                </div>
                @if ($transaction->payment_reference)
                    <div>
                        <dt class="text-sm font-medium text-slate-500">Referensi Pembayaran</dt>
                        <dd class="mt-1 text-lg font-semibold text-slate-900">{{ $transaction->payment_reference }}</dd>
                    </div>
                @endif
            @endif
            <div>
                <dt class="text-sm font-medium text-slate-500">Nominal</dt>
                <dd class="mt-1 text-lg font-semibold {{ $transaction->type === 'deposit' ? 'text-green-600' : 'text-red-600' }}">
                    {{ $transaction->type === 'deposit' ? '+' : '-' }}{{ 'Rp '.number_format($transaction->amount, 2, ',', '.') }}
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-slate-500">Saldo Setelah Transaksi</dt>
                <dd class="mt-1 text-lg font-semibold text-slate-900">
                    {{ 'Rp '.number_format($transaction->running_balance, 2, ',', '.') }}
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-slate-500">Pemilik Tabungan</dt>
                <dd class="mt-1 text-lg font-semibold text-slate-900">{{ $transaction->savingsAccount->user->name }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-slate-500">No. Rekening</dt>
                <dd class="mt-1 text-lg font-semibold text-slate-900">{{ $transaction->savingsAccount->account_number }}</dd>
            </div>
        </dl>

        @if ($transaction->type === 'deposit' && $transaction->payment_status === \App\Models\Transaction::STATUS_PENDING && $transaction->payment_url)
            <div class="mt-6 flex items-center justify-between rounded-xl bg-amber-50 px-4 py-3 text-amber-700">
                <div>
                    <p class="font-semibold">Pembayaran Midtrans belum selesai.</p>
                    <p class="text-sm">Klik tombol di samping untuk melanjutkan proses pembayaran.</p>
                </div>
                <a href="{{ $transaction->payment_url }}" target="_blank" class="rounded-xl bg-amber-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-amber-700">Bayar Sekarang</a>
            </div>
        @endif

        @if ($transaction->note)
            <div class="mt-6 rounded-xl bg-slate-50 p-4">
                <h3 class="text-sm font-semibold text-slate-600">Catatan</h3>
                <p class="mt-2 text-slate-700">{{ $transaction->note }}</p>
            </div>
        @endif
    </x-card>
</x-layouts.app>
