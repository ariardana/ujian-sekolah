@props(['title' => null, 'subtitle' => null])
<section class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
    @if ($title)
        <header class="flex items-start justify-between gap-3 border-b border-slate-200 px-5 py-4 dark:border-slate-800">
            <div>
                <h2 class="text-base font-semibold">{{ $title }}</h2>
                @if ($subtitle)
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $subtitle }}</p>
                @endif
            </div>
            @isset($actions)
                <div class="flex items-center gap-2">{{ $actions }}</div>
            @endisset
        </header>
    @endif
    <div {{ $attributes->merge(['class' => 'p-5']) }}>
        {{ $slot }}
    </div>
</section>
