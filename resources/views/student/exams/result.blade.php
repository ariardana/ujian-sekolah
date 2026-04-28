<x-app-layout>
    <x-slot name="header">
        <x-page-title :title="'Hasil: '.$exam->name" :subtitle="$exam->mapel?->name" />
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <x-card>
            @if (!$result)
                <div class="text-center py-10">
                    <div class="mx-auto h-12 w-12 rounded-full bg-amber-100 grid place-items-center text-amber-600 dark:bg-amber-900/30 dark:text-amber-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="mt-3 font-medium">Ujian sudah disubmit.</p>
                    <p class="text-sm text-slate-500 mt-1">Menunggu penilaian guru untuk soal essay.</p>
                </div>
            @else
                <div class="text-center py-6">
                    <p class="text-xs uppercase tracking-widest text-slate-500">Skor Anda</p>
                    <p class="mt-2 text-5xl font-bold tabular-nums {{ $result->is_passed ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ number_format($result->percentage, 1) }}<span class="text-2xl">%</span>
                    </p>
                    <p class="mt-1 text-sm text-slate-500">{{ $result->total_score }} / {{ $result->max_score }} poin</p>
                    @if ($result->is_passed)
                        <span class="mt-3 inline-block rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">LULUS</span>
                    @else
                        <span class="mt-3 inline-block rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-700 dark:bg-rose-900/40 dark:text-rose-300">BELUM LULUS</span>
                    @endif
                </div>

                <div class="grid grid-cols-3 gap-3 mt-4">
                    <div class="rounded-xl bg-emerald-50 p-3 text-center dark:bg-emerald-900/20">
                        <p class="text-2xl font-bold text-emerald-600">{{ $result->correct_count }}</p>
                        <p class="text-xs text-emerald-700 dark:text-emerald-400">Benar</p>
                    </div>
                    <div class="rounded-xl bg-rose-50 p-3 text-center dark:bg-rose-900/20">
                        <p class="text-2xl font-bold text-rose-600">{{ $result->wrong_count }}</p>
                        <p class="text-xs text-rose-700 dark:text-rose-400">Salah</p>
                    </div>
                    <div class="rounded-xl bg-slate-100 p-3 text-center dark:bg-slate-800/60">
                        <p class="text-2xl font-bold text-slate-600 dark:text-slate-300">{{ $result->unanswered_count }}</p>
                        <p class="text-xs text-slate-500">Kosong</p>
                    </div>
                </div>

                @if (!$result->graded_at)
                    <p class="mt-4 text-center text-xs text-amber-600 dark:text-amber-400">
                        Beberapa soal essay masih menunggu penilaian guru.
                    </p>
                @endif
            @endif

            <div class="mt-6 flex justify-center">
                <x-btn variant="secondary" :href="route('student.dashboard')">Kembali ke Dashboard</x-btn>
            </div>
        </x-card>
    </div>
</x-app-layout>
