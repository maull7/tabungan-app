<x-layouts.app>
    @php($isPending = $transaction->payment_status === \App\Models\Transaction::STATUS_PENDING)
    @php($statusLabel = match ($transaction->payment_status) {
        \App\Models\Transaction::STATUS_COMPLETED => 'Lunas',
        \App\Models\Transaction::STATUS_FAILED => 'Gagal',
        default => 'Menunggu',
    })
    @php($statusClass = match ($transaction->payment_status) {
        \App\Models\Transaction::STATUS_COMPLETED => 'text-emerald-600',
        \App\Models\Transaction::STATUS_FAILED => 'text-rose-600',
        default => 'text-amber-600',
    })

    <div class="space-y-6">
        <div class="flex flex-col gap-2">
            <h2 class="text-2xl font-bold text-slate-900">Simulasi Pembayaran Midtrans</h2>
            <p class="text-sm text-slate-500">Halaman ini hanya tersedia pada mode simulasi. Gunakan untuk menguji alur pembayaran tanpa terhubung ke Midtrans asli.</p>
        </div>

        <x-card class="space-y-4">
            <dl class="grid gap-4 md:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-slate-500">Nomor Struk</dt>
                    <dd class="mt-1 text-lg font-semibold text-slate-900">{{ $transaction->receipt_number }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-slate-500">Status Saat Ini</dt>
                    <dd class="mt-1 text-lg font-semibold {{ $statusClass }}">
                        {{ $statusLabel }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-slate-500">Nominal</dt>
                    <dd class="mt-1 text-lg font-semibold text-emerald-600">Rp {{ number_format($transaction->amount, 2, ',', '.') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-slate-500">Pemilik</dt>
                    <dd class="mt-1 text-lg font-semibold text-slate-900">{{ $transaction->savingsAccount->user->name }}</dd>
                </div>
            </dl>

            <div class="rounded-xl bg-slate-50 p-4">
                <h3 class="text-sm font-semibold text-slate-600">Cara Pengujian</h3>
                @if ($isPending)
                    <ol class="mt-2 list-decimal space-y-1 pl-5 text-sm text-slate-600">
                        <li>Buka halaman detail transaksi di tab lain untuk memantau perubahan status.</li>
                        <li>Pilih salah satu aksi di bawah untuk menandai pembayaran berhasil atau gagal.</li>
                        <li>Status transaksi akan diperbarui seketika tanpa memanggil Midtrans asli.</li>
                    </ol>
                @else
                    <p class="mt-2 text-sm text-slate-600">Status pembayaran sudah <span class="font-semibold">{{ $transaction->payment_status }}</span> sehingga tidak ada aksi simulasi yang tersedia.</p>
                @endif
            </div>

            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div class="text-sm text-slate-600">
                    <p>Setelah memilih aksi, Anda akan diarahkan kembali ke detail transaksi.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    @if ($isPending)
                        <form method="POST" action="{{ route('midtrans.simulations.store', $transaction) }}">
                            @csrf
                            <input type="hidden" name="status" value="success">
                            <x-button type="submit">Tandai Berhasil</x-button>
                        </form>
                        <form method="POST" action="{{ route('midtrans.simulations.store', $transaction) }}">
                            @csrf
                            <input type="hidden" name="status" value="failed">
                            <x-button type="submit" variant="danger">Tandai Gagal</x-button>
                        </form>
                    @endif
                    <x-button href="{{ route('transactions.show', $transaction) }}" variant="secondary">Kembali ke Detail</x-button>
                </div>
            </div>
        </x-card>
    </div>
</x-layouts.app>
