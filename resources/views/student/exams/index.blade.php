<x-app-layout>
    <x-slot name="header">
        <x-page-title title="Ujian Tersedia" subtitle="Pilih ujian untuk dikerjakan" />
    </x-slot>

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse ($exams as $e)
            <x-card>
                <p class="text-xs uppercase tracking-wider text-brand-600 dark:text-brand-400">{{ $e->mapel?->name }}</p>
                <h3 class="text-lg font-semibold mt-1">{{ $e->name }}</h3>
                <dl class="mt-3 space-y-1 text-sm text-slate-600 dark:text-slate-400">
                    <div class="flex justify-between"><dt>Mulai</dt><dd>{{ $e->start_at?->format('d M Y H:i') }}</dd></div>
                    <div class="flex justify-between"><dt>Selesai</dt><dd>{{ $e->end_at?->format('d M Y H:i') }}</dd></div>
                    <div class="flex justify-between"><dt>Durasi</dt><dd>{{ $e->duration_minutes }} menit</dd></div>
                    @if ($e->token)
                        <div class="flex justify-between"><dt>Token</dt><dd>Diperlukan</dd></div>
                    @endif
                </dl>
                <div class="mt-4">
                    @if (now()->lt($e->start_at))
                        <span class="text-xs text-amber-600 dark:text-amber-400">Belum dibuka</span>
                    @else
                        <x-btn variant="primary" :href="route('student.exams.show', $e)">Mulai Ujian</x-btn>
                    @endif
                </div>
            </x-card>
        @empty
            <div class="col-span-full">
                <x-card>
                    <p class="text-center text-slate-500 py-8">Tidak ada ujian tersedia saat ini.</p>
                </x-card>
            </div>
        @endforelse
    </div>
    <div class="mt-4">{{ $exams->links() }}</div>
</x-app-layout>
