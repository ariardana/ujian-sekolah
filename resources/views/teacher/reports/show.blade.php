<x-app-layout>
    <x-slot name="header">
        <x-page-title :title="'Laporan: '.$exam->name" :subtitle="$exam->mapel?->name">
            <x-btn variant="secondary" :href="route('teacher.reports.excel', $exam)">Export Excel</x-btn>
            <x-btn variant="primary" :href="route('teacher.reports.pdf', $exam)">Export PDF</x-btn>
        </x-page-title>
    </x-slot>

    <div class="grid sm:grid-cols-2 lg:grid-cols-5 gap-3 mb-4">
        <x-stat-card label="Peserta" :value="$stats['count']" color="brand" />
        <x-stat-card label="Lulus" :value="$stats['passed']" color="emerald" />
        <x-stat-card label="Rata-rata" :value="$stats['avg'].'%'" color="indigo" />
        <x-stat-card label="Tertinggi" :value="$stats['max'].'%'" color="amber" />
        <x-stat-card label="Terendah" :value="$stats['min'].'%'" color="rose" />
    </div>

    <x-card title="Ranking">
        <div class="overflow-x-auto -mx-5">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800/60">
                    <tr class="text-left text-xs font-semibold uppercase text-slate-500">
                        <th class="px-5 py-3">#</th>
                        <th class="px-5 py-3">Nama</th>
                        <th class="px-5 py-3">NISN</th>
                        <th class="px-5 py-3">Kelas</th>
                        <th class="px-5 py-3">Skor</th>
                        <th class="px-5 py-3">Persentase</th>
                        <th class="px-5 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @foreach ($participants as $i => $p)
                        <tr>
                            <td class="px-5 py-3 font-bold">{{ $i + 1 }}</td>
                            <td class="px-5 py-3 font-medium">{{ $p->user->name }}</td>
                            <td class="px-5 py-3 font-mono text-xs">{{ $p->user->nisn }}</td>
                            <td class="px-5 py-3 text-xs text-slate-500">
                                @if ($p->user->student?->schoolClass)
                                    {{ $p->user->student->schoolClass->level }} {{ $p->user->student->schoolClass->name }}
                                @else - @endif
                            </td>
                            <td class="px-5 py-3">{{ $p->result?->total_score ?? '-' }} / {{ $p->result?->max_score ?? '-' }}</td>
                            <td class="px-5 py-3 font-semibold">{{ $p->result ? number_format($p->result->percentage, 1).'%' : '-' }}</td>
                            <td class="px-5 py-3">
                                @if ($p->result?->is_passed)
                                    <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">Lulus</span>
                                @elseif ($p->result)
                                    <span class="rounded-full bg-rose-100 px-2 py-0.5 text-xs text-rose-700 dark:bg-rose-900/40 dark:text-rose-300">Belum Lulus</span>
                                @else
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-600 dark:bg-slate-800 dark:text-slate-300">Belum Dinilai</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>
</x-app-layout>
