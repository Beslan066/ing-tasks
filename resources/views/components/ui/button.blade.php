{{-- resources/views/components/form/button.blade.php --}}
@props([
    'type'    => 'button',
    'variant' => 'primary',
    'icon'    => null,
])

@php
    $baseClasses = 'transition-all duration-200 focus:outline-none max-[500px]:w-full flex items-center justify-center';

    $variants = [
        'primary' => 'px-5 py-2.5 rounded-xl text-[13px] font-medium bg-emerald-500 hover:bg-emerald-600 text-white focus:ring-4 focus:ring-emerald-500/20 shadow-sm hover:shadow-md',
        'secondary' => 'px-5 py-2.5 rounded-xl text-[13px] font-medium text-slate-600 hover:text-slate-800 hover:bg-slate-50 focus:ring-2 focus:ring-slate-200',
        'action' => 'px-3 py-2.5 rounded-xl text-[13px] font-semibold text-white bg-emerald-500 hover:bg-emerald-600 shadow-md shadow-emerald-500/20 hover:shadow-lg hover:shadow-emerald-500/30',
        'danger-outline' => 'px-3 py-2.5 rounded-xl text-[13px] font-semibold bg-white border-2 border-rose-200 text-rose-600 hover:bg-rose-50 hover:border-rose-300',
        'ghost' => 'px-3 py-2 rounded-lg text-[12px] font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100',
    ];

    $iconSizes = [
        'ghost' => '',
    ];

    $classes     = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']);
    $iconClasses = $iconSizes[$variant] ?? 'text-[11px]';
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    @if ($icon)
        <i class="{{ $icon }} {{ $iconClasses }} mr-1.5" aria-hidden="true"></i>
    @endif

    {{ $slot }}
</button>
