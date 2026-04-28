<x-app-layout>
    <x-slot name="header">
        <x-page-title :title="'Grading: '.$exam->name">
            <x-btn variant="secondary" :href="route('teacher.reports.show', $exam)">Lihat Laporan</x-btn>
        </x-page-title>
    </x-slot>

    <x-card>
        <div class="overflow-x-auto -mx-5">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800/60">
                    <tr class="text-left text-xs font-semibold uppercase text-slate-500">
                        <th class="px-5 py-3">Siswa</th>
                        <th class="px-5 py-3">NISN</th>
                        <th class="px-5 py-3">Mulai</th>
                        <th class="px-5 py-3">Submit</th>
                        <th class="px-5 py-3">Nilai</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse ($participants as $p)
                        <tr>
                            <td class="px-5 py-3 font-medium">{{ $p->user->name }}</td>
                            <td class="px-5 py-3 font-mono text-xs">{{ $p->user->nisn }}</td>
                            <td class="px-5 py-3 text-xs text-slate-500">{{ $p->started_at?->format('d M H:i') ?: '-' }}</td>
                            <td class="px-5 py-3 text-xs text-slate-500">{{ $p->submitted_at?->format('d M H:i') ?: '-' }}</td>
                            <td class="px-5 py-3 font-semibold">
                                {{ $p->result?->total_score ?? '-' }}/{{ $p->result?->max_score ?? '-' }}
                                @if ($p->result)
                                    <span class="text-xs text-slate-500">({{ number_format($p->result->percentage, 1) }}%)</span>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                <span class="rounded-full px-2 py-0.5 text-xs font-medium
                                    @class([
                                        'bg-slate-100 text-slate-600' => $p->status === 'not_started',
                                        'bg-amber-100 text-amber-700' => $p->status === 'in_progress',
                                        'bg-blue-100 text-blue-700' => $p->status === 'submitted',
                                        'bg-emerald-100 text-emerald-700' => $p->status === 'graded',
                                    ])">{{ $p->status }}</span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('teacher.grading.show', [$exam, $p]) }}" class="text-brand-600 hover:underline">Nilai</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-8 text-center text-slate-500">Belum ada peserta.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $participants->links() }}</div>
    </x-card>
</x-app-layout>
