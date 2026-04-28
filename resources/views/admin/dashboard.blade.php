<x-app-layout>
    <x-slot name="header">
        <x-page-title title="Dashboard Admin" subtitle="Ringkasan sistem ujian sekolah" />
    </x-slot>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <x-stat-card label="Total Siswa" :value="$stats['students']" color="brand"
            icon="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-5.13a4 4 0 100-8 4 4 0 000 8zm6 0a4 4 0 11-8 0 4 4 0 018 0z" />
        <x-stat-card label="Total Guru" :value="$stats['teachers']" color="emerald"
            icon="M21 13.255A23.93 23.93 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h0" />
        <x-stat-card label="Total Ujian" :value="$stats['exams']" color="indigo"
            icon="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
        <x-stat-card label="Pengguna Online" :value="$stats['online_users']" color="amber"
            icon="M5 13l4 4L19 7" />
    </div>

    <div class="mt-4 grid grid-cols-2 lg:grid-cols-4 gap-4">
        <x-stat-card label="Kelas" :value="$stats['classes']" color="sky" icon="M19 11H5m14-7H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V6a2 2 0 00-2-2z" />
        <x-stat-card label="Mapel" :value="$stats['mapels']" color="brand" icon="M12 6.253v13" />
        <x-stat-card label="Ujian Aktif" :value="$stats['exams_published']" color="emerald" icon="M5 13l4 4L19 7" />
        <x-stat-card label="Rata-rata Nilai" :value="$stats['avg_score']" color="rose" icon="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2z" />
    </div>

    <div class="mt-6 grid lg:grid-cols-2 gap-4">
        <x-card title="Ujian Terbaru">
            <ul class="divide-y divide-slate-200 dark:divide-slate-800 -mx-5">
                @forelse ($recentExams as $exam)
                    <li class="flex items-center justify-between gap-3 px-5 py-3">
                        <div class="min-w-0">
                            <p class="font-medium truncate">{{ $exam->name }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                {{ $exam->mapel?->name ?? '-' }} ·
                                {{ $exam->schoolClass ? $exam->schoolClass->level.' '.$exam->schoolClass->name : 'Semua kelas' }}
                            </p>
                        </div>
                        <span class="rounded-full px-2.5 py-0.5 text-xs font-medium
                            @class([
                                'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' => $exam->status === 'published',
                                'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300' => $exam->status === 'draft',
                                'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300' => $exam->status === 'closed',
                            ])">{{ $exam->status }}</span>
                    </li>
                @empty
                    <li class="px-5 py-6 text-center text-sm text-slate-500">Belum ada ujian.</li>
                @endforelse
            </ul>
        </x-card>

        <x-card title="Login Terbaru">
            <ul class="divide-y divide-slate-200 dark:divide-slate-800 -mx-5">
                @forelse ($recentLogins as $u)
                    <li class="flex items-center gap-3 px-5 py-3">
                        <div class="h-8 w-8 rounded-full bg-brand-600 text-white grid place-items-center text-xs font-bold">
                            {{ strtoupper(substr($u->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium truncate">{{ $u->name }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 capitalize">{{ $u->role }}</p>
                        </div>
                        <span class="text-xs text-slate-500">{{ $u->last_login_at?->diffForHumans() }}</span>
                    </li>
                @empty
                    <li class="px-5 py-6 text-center text-sm text-slate-500">Belum ada aktivitas.</li>
                @endforelse
            </ul>
        </x-card>
    </div>
</x-app-layout>
