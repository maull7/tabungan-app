@props([
    'variant' => 'primary',
    'type' => 'button',
    'loading' => false,
])

@php
    $base = 'inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2 font-semibold transition focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-60 disabled:cursor-not-allowed';
    $variants = [
        'primary' => 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-200',
        'secondary' => 'bg-slate-100 text-slate-700 hover:bg-slate-200 focus:ring-slate-200',
        'danger' => 'bg-red-500 text-white hover:bg-red-600 focus:ring-red-200',
    ];
    $class = $base.' '.($variants[$variant] ?? $variants['primary']);
    $tag = $attributes->has('href') ? 'a' : 'button';
@endphp

@if ($tag === 'a')
    <a {{ $attributes->merge(['class' => $class]) }}>
        @if($loading)
            <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>
        @endif
        <span>{{ $slot }}</span>
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $class]) }} @disabled($loading)>
        @if($loading)
            <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>
        @endif
        <span>{{ $slot }}</span>
    </button>
@endif
