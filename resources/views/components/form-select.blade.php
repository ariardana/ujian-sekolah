@props(['label' => null, 'name', 'value' => null, 'help' => null])
<div>
    @if ($label)
        <label for="{{ $name }}" class="block text-sm font-medium mb-1">{{ $label }}</label>
    @endif
    <select id="{{ $name }}" name="{{ $name }}"
        {{ $attributes->merge(['class' => 'block w-full rounded-lg border-slate-300 bg-white shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100']) }}>
        {{ $slot }}
    </select>
    @if ($help)
        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $help }}</p>
    @endif
    <x-input-error :messages="$errors->get($name)" class="mt-2" />
</div>
