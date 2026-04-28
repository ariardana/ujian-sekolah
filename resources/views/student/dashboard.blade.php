<x-app-layout>
    <x-slot name="header">
        <x-page-title title="Dashboard Siswa" subtitle="Halo {{ auth()->user()->name }}, semangat belajar!" />
    </x-slot>

    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
        <x-stat-card label="Ujian Tersedia" :value="$stats['available']" color="brand"
            icon="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5" />
        <x-stat-card label="Ujian Selesai" :value="$stats['completed']" color="emerald"
            icon="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
        <x-stat-card label="Rata-rata Nilai" :value="$stats['avg_score']" color="amber"
            icon="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2z" />
    </div>

    <div class="mt-6 grid lg:grid-cols-2 gap-4">
        <x-card title="Ujian Aktif" subtitle="Ujian yang dapat Anda kerjakan">
            <ul class="divide-y divide-slate-200 dark:divide-slate-800 -mx-5">
                @forelse ($activeExams as $exam)
                    <li class="flex items-center justify-between gap-3 px-5 py-3">
                        <div class="min-w-0">
                            <p class="font-medium truncate">{{ $exam->name }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                {{ $exam->mapel?->name }} · {{ $exam->duration_minutes }} menit ·
                                Berakhir {{ $exam->end_at->format('d M Y H:i') }}
                            </p>
                        </div>
                        <x-btn :href="route('student.exams.show', $exam)" variant="primary">Mulai</x-btn>
                    </li>
                @empty
                    <li class="px-5 py-6 text-center text-sm text-slate-500">Belum ada ujian aktif.</li>
                @endforelse
            </ul>
        </x-card>

        <x-card title="Riwayat Nilai Terbaru">
            <ul class="divide-y divide-slate-200 dark:divide-slate-800 -mx-5">
                @forelse ($recentResults as $result)
                    @php $exam = $result->participant->exam; @endphp
                    <li class="flex items-center justify-between gap-3 px-5 py-3">
                        <div class="min-w-0">
                            <p class="font-medium truncate">{{ $exam->name }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $exam->mapel?->name }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold {{ $result->is_passed ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ number_format($result->percentage, 1) }}
                            </p>
                            <p class="text-xs text-slate-500">{{ $result->is_passed ? 'LULUS' : 'TIDAK LULUS' }}</p>
                        </div>
                    </li>
                @empty
                    <li class="px-5 py-6 text-center text-sm text-slate-500">Belum ada riwayat.</li>
                @endforelse
            </ul>
        </x-card>
    </div>
</x-app-layout>
