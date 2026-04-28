@props(['label' => null, 'name', 'type' => 'text', 'value' => null, 'placeholder' => null, 'help' => null])
<div>
    @if ($label)
        <label for="{{ $name }}" class="block text-sm font-medium mb-1">{{ $label }}</label>
    @endif
    <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge(['class' => 'block w-full rounded-lg border-slate-300 bg-white shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100']) }}>
    @if ($help)
        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $help }}</p>
    @endif
    <x-input-error :messages="$errors->get($name)" class="mt-2" />
</div>
