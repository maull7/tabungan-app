@props([
    'title' => 'Konfirmasi',
])

<div class="inline" data-modal>
    <div data-modal-trigger>
        {{ $trigger ?? $slot }}
    </div>
    <div class="fixed inset-0 z-40 hidden items-center justify-center bg-slate-900/60 p-4" data-modal-backdrop>
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
            <div class="flex items-start justify-between gap-4">
                <h3 class="text-lg font-semibold text-slate-800">{{ $title }}</h3>
                <button type="button" class="text-slate-400 hover:text-slate-600" data-modal-close>&times;</button>
            </div>
            <div class="mt-4 text-slate-600">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
