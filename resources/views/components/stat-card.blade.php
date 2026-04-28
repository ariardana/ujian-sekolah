@props(['label', 'value', 'icon' => null, 'color' => 'brand'])
@php
    $palette = [
        'brand' => 'from-brand-500 to-brand-600 text-white',
        'emerald' => 'from-emerald-500 to-emerald-600 text-white',
        'amber' => 'from-amber-500 to-amber-600 text-white',
        'rose' => 'from-rose-500 to-rose-600 text-white',
        'indigo' => 'from-indigo-500 to-indigo-600 text-white',
        'sky' => 'from-sky-500 to-sky-600 text-white',
    ][$color] ?? 'from-slate-500 to-slate-600 text-white';
@endphp
<div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
    <div class="flex items-center justify-between">
        <p class="text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ $label }}</p>
        @if ($icon)
            <div class="grid place-items-center h-9 w-9 rounded-lg bg-gradient-to-br {{ $palette }}">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                </svg>
            </div>
        @endif
    </div>
    <p class="mt-2 text-3xl font-bold tracking-tight">{{ $value }}</p>
    @if (isset($foot))
        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $foot }}</p>
    @endif
</div>
