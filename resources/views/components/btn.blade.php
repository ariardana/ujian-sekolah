@props(['variant' => 'primary', 'href' => null, 'type' => 'button'])
@php
    $variants = [
        'primary' => 'bg-brand-600 hover:bg-brand-700 text-white shadow-sm shadow-brand-600/30',
        'secondary' => 'bg-white hover:bg-slate-50 text-slate-700 ring-1 ring-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-200 dark:ring-slate-700',
        'danger' => 'bg-rose-600 hover:bg-rose-700 text-white',
        'ghost' => 'bg-transparent hover:bg-slate-100 text-slate-700 dark:hover:bg-slate-800 dark:text-slate-200',
        'success' => 'bg-emerald-600 hover:bg-emerald-700 text-white',
    ][$variant] ?? '';
    $base = 'inline-flex items-center gap-2 rounded-lg px-3.5 py-2 text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2';
@endphp
@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => "$base $variants"]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => "$base $variants"]) }}>{{ $slot }}</button>
@endif
