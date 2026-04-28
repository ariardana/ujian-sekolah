<x-app-layout>
    <x-slot name="header">
        <x-page-title title="Laporan Ujian" />
    </x-slot>

    <x-card>
        <div class="overflow-x-auto -mx-5">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800/60">
                    <tr class="text-left text-xs font-semibold uppercase text-slate-500">
                        <th class="px-5 py-3">Nama Ujian</th>
                        <th class="px-5 py-3">Mapel</th>
                        <th class="px-5 py-3">Kelas</th>
                        <th class="px-5 py-3">Peserta</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse ($exams as $e)
                        <tr>
                            <td class="px-5 py-3 font-medium">{{ $e->name }}</td>
                            <td class="px-5 py-3">{{ $e->mapel?->name }}</td>
                            <td class="px-5 py-3">{{ $e->schoolClass ? $e->schoolClass->level.' '.$e->schoolClass->name : 'Semua' }}</td>
                            <td class="px-5 py-3">{{ $e->participants_count }}</td>
                            <td class="px-5 py-3 text-right whitespace-nowrap">
                                <a href="{{ route('teacher.reports.show', $e) }}" class="text-brand-600 hover:underline">Detail</a>
                                <a href="{{ route('teacher.reports.excel', $e) }}" class="text-emerald-600 hover:underline ml-2">Excel</a>
                                <a href="{{ route('teacher.reports.pdf', $e) }}" class="text-rose-600 hover:underline ml-2">PDF</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-8 text-center text-slate-500">Belum ada ujian.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $exams->links() }}</div>
    </x-card>
</x-app-layout>
