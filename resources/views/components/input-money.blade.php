@props([
    'name',
    'label' => null,
    'value' => null,
    'placeholder' => 'Masukkan nominal',
])

@php
    $inputId = $attributes->get('id', $name.'-input');
    $formatted = $value !== null ? number_format((float) $value, 2, ',', '.') : '';
@endphp

<div class="space-y-2" data-money-input>
    @if($label)
        <label for="{{ $inputId }}" class="block text-sm font-medium text-slate-700">{{ $label }}</label>
    @endif
    <div class="relative">
        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">Rp</span>
        <input
            type="text"
            id="{{ $inputId }}"
            autocomplete="off"
            inputmode="decimal"
            data-money-display
            class="w-full rounded-xl border border-slate-200 bg-white py-3 pl-10 pr-4 text-lg font-semibold text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
            placeholder="{{ $placeholder }}"
            value="{{ $formatted }}"
        >
        <input type="hidden" name="{{ $name }}" data-money-hidden value="{{ $value }}">
    </div>
    @error($name)
        <p class="text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
