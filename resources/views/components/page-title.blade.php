@props(['title', 'subtitle' => null])
<div class="flex items-start justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold tracking-tight">{{ $title }}</h1>
        @if ($subtitle)
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $subtitle }}</p>
        @endif
    </div>
    <div class="flex items-center gap-2">
        {{ $slot }}
    </div>
</div>
