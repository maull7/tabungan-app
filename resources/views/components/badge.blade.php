@props(['variant' => 'primary'])

@php
    $base = 'inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-wide';
    $variants = [
        'success' => 'bg-green-100 text-green-700',
        'warning' => 'bg-yellow-100 text-yellow-700',
        'info' => 'bg-blue-100 text-blue-700',
    ];
    $class = $base.' '.($variants[$variant] ?? $variants['info']);
@endphp

<span {{ $attributes->merge(['class' => $class]) }}>
    {{ $slot }}
</span>
