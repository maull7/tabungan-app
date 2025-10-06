<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Tabungan') }}</title>
    <script src="https://cdn.tailwindcss.com?plugins=typography,forms"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="flex min-h-full flex-col">
    <div class="flex-1">
        <div class="mx-auto flex w-full max-w-6xl flex-col gap-6 px-4 py-10">
            <header class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">{{ config('app.name', 'Tabungan') }}</h1>
                    <p class="text-sm text-slate-500">Kelola tabungan Anda dengan mudah.</p>
                </div>
                <nav class="flex items-center gap-3 text-sm text-slate-600">
                    @php($user = auth()->user())
                    <a href="{{ route('dashboard') }}" class="rounded-xl px-3 py-2 font-semibold hover:bg-white {{ request()->routeIs('dashboard') ? 'bg-white text-blue-600' : '' }}">Dashboard</a>
                    <a href="{{ route('transactions.index') }}" class="rounded-xl px-3 py-2 font-semibold hover:bg-white {{ request()->routeIs('transactions.*') ? 'bg-white text-blue-600' : '' }}">Transaksi</a>
                    @if ($user?->is_admin)
                        <a href="{{ route('admin.dashboard') }}" class="rounded-xl px-3 py-2 font-semibold hover:bg-white {{ request()->routeIs('admin.*') ? 'bg-white text-blue-600' : '' }}">Admin</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <x-button type="submit" variant="secondary">Keluar</x-button>
                    </form>
                </nav>
            </header>

            @if (session('status'))
                <div class="rounded-xl bg-green-100 px-4 py-3 text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-xl bg-red-100 px-4 py-3 text-red-700">
                    <p class="font-semibold">Terjadi kesalahan:</p>
                    <ul class="mt-2 list-disc pl-5 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <main class="space-y-6">
                {{ $slot }}
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-money-input]').forEach(container => {
                const display = container.querySelector('[data-money-display]');
                const hidden = container.querySelector('[data-money-hidden]');
                const format = (value) => {
                    if (value === '') {
                        hidden.value = '';
                        return '';
                    }
                    const numeric = value.replace(/[^0-9.,]/g, '').replace(/\./g, '').replace(/,/g, '.');
                    const number = Number(numeric);
                    if (Number.isNaN(number)) return '';
                    hidden.value = number.toFixed(2);
                    return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(number);
                };
                display.addEventListener('input', () => {
                    display.value = format(display.value);
                });
                display.addEventListener('blur', () => {
                    display.value = format(display.value);
                });
            });

            document.querySelectorAll('[data-modal]').forEach(modal => {
                const trigger = modal.querySelector('[data-modal-trigger]');
                const backdrop = modal.querySelector('[data-modal-backdrop]');
                const closeButtons = modal.querySelectorAll('[data-modal-close]');

                const open = () => backdrop.classList.remove('hidden');
                const close = () => backdrop.classList.add('hidden');

                trigger?.addEventListener('click', (event) => {
                    event.preventDefault();
                    open();
                });

                closeButtons.forEach(btn => btn.addEventListener('click', close));
                backdrop?.addEventListener('click', (event) => {
                    if (event.target === backdrop) {
                        close();
                    }
                });
            });
        });
    </script>
</body>
</html>
