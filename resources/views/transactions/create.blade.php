<x-layouts.app>
    <div class="grid gap-6 lg:grid-cols-2">
        <x-card>
            <h2 class="mb-4 text-xl font-semibold text-slate-900">Setor Dana</h2>
            <form method="POST" action="{{ route('transactions.store') }}" class="space-y-4">
                @csrf
                <x-input-money name="amount" label="Nominal Setoran" value="{{ old('amount', 100000) }}" />
                <div>
                    <label for="note" class="mb-1 block text-sm font-medium text-slate-600">Catatan (opsional)</label>
                    <textarea id="note" name="note" rows="3" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" placeholder="Contoh: Setoran gaji bulan ini">{{ old('note') }}</textarea>
                </div>
                <x-button type="submit" class="w-full justify-center">Simpan Setoran</x-button>
            </form>
        </x-card>

        <x-card>
            <h2 class="mb-4 text-xl font-semibold text-slate-900">Tarik Dana</h2>
            <form method="POST" action="{{ route('transactions.withdraw') }}" class="space-y-4">
                @csrf
                <x-input-money name="amount" label="Nominal Penarikan" value="{{ old('amount') }}" />
                <div>
                    <label for="withdraw-note" class="mb-1 block text-sm font-medium text-slate-600">Catatan (opsional)</label>
                    <textarea id="withdraw-note" name="note" rows="3" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" placeholder="Contoh: Penarikan kebutuhan"></textarea>
                </div>
                <x-button type="submit" class="w-full justify-center" variant="secondary">Simpan Penarikan</x-button>
            </form>
        </x-card>
    </div>
</x-layouts.app>
