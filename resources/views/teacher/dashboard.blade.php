<x-app-layout>
    <x-slot name="header">
        <x-page-title title="Dashboard Guru" subtitle="Aktivitas mengajar dan ujian Anda" />
    </x-slot>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <x-stat-card label="Mapel Diampu" :value="$stats['mapels']" color="brand"
            icon="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253" />
        <x-stat-card label="Total Soal" :value="$stats['questions']" color="indigo"
            icon="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        <x-stat-card label="Ujian Dibuat" :value="$stats['exams']" color="emerald"
            icon="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5" />
        <x-stat-card label="Ujian Aktif" :value="$stats['exams_active']" color="amber"
            icon="M13 10V3L4 14h7v7l9-11h-7z" />
    </div>

    <div class="mt-6 grid lg:grid-cols-2 gap-4">
        <x-card title="Ujian Saya">
            <x-slot name="actions">
                <x-btn variant="primary" :href="route('teacher.exams.create')">+ Buat Ujian</x-btn>
            </x-slot>
            <ul class="divide-y divide-slate-200 dark:divide-slate-800 -mx-5">
                @forelse ($recentExams as $exam)
                    <li class="flex items-center justify-between gap-3 px-5 py-3">
                        <div class="min-w-0">
                            <a href="{{ route('teacher.exams.questions', $exam) }}" class="font-medium hover:text-brand-600 truncate">{{ $exam->name }}</a>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                {{ $exam->mapel?->name }} · {{ $exam->start_at?->format('d M Y H:i') }}
                            </p>
                        </div>
                        <span class="rounded-full px-2.5 py-0.5 text-xs font-medium
                            @class([
                                'bg-emerald-100 text-emerald-700' => $exam->status === 'published',
                                'bg-slate-100 text-slate-600' => $exam->status === 'draft',
                                'bg-rose-100 text-rose-700' => $exam->status === 'closed',
                            ])">{{ $exam->status }}</span>
                    </li>
                @empty
                    <li class="px-5 py-6 text-center text-sm text-slate-500">Belum ada ujian.</li>
                @endforelse
            </ul>
        </x-card>

        <x-card title="Mapel Saya">
            <ul class="divide-y divide-slate-200 dark:divide-slate-800 -mx-5">
                @forelse ($mapels as $mapel)
                    <li class="flex items-center justify-between gap-3 px-5 py-3">
                        <div>
                            <p class="font-medium">{{ $mapel->name }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $mapel->code }}</p>
                        </div>
                        <span class="text-xs font-medium text-slate-600 dark:text-slate-300">
                            {{ $mapel->questions_count }} soal
                        </span>
                    </li>
                @empty
                    <li class="px-5 py-6 text-center text-sm text-slate-500">Belum ada mapel diampu.</li>
                @endforelse
            </ul>
        </x-card>
    </div>
</x-app-layout>
