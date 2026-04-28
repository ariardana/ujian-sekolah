<x-app-layout>
    <x-slot name="header">
        <x-page-title title="Ujian">
            <x-btn variant="primary" :href="route('teacher.exams.create')">+ Buat Ujian</x-btn>
        </x-page-title>
    </x-slot>

    <x-card>
        <div class="overflow-x-auto -mx-5">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800/60">
                    <tr class="text-left text-xs font-semibold uppercase text-slate-500">
                        <th class="px-5 py-3">Nama</th>
                        <th class="px-5 py-3">Mapel</th>
                        <th class="px-5 py-3">Kelas</th>
                        <th class="px-5 py-3">Mulai</th>
                        <th class="px-5 py-3">Soal</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse ($exams as $e)
                        <tr>
                            <td class="px-5 py-3 font-medium">{{ $e->name }}</td>
                            <td class="px-5 py-3">{{ $e->mapel?->name }}</td>
                            <td class="px-5 py-3">{{ $e->schoolClass ? $e->schoolClass->level.' '.$e->schoolClass->name : '— Semua' }}</td>
                            <td class="px-5 py-3 text-xs text-slate-500">{{ $e->start_at?->format('d M Y H:i') }}</td>
                            <td class="px-5 py-3">{{ $e->questions_count }}</td>
                            <td class="px-5 py-3">
                                <span class="rounded-full px-2 py-0.5 text-xs font-medium
                                    @class([
                                        'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' => $e->status === 'published',
                                        'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300' => $e->status === 'draft',
                                        'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300' => $e->status === 'closed',
                                    ])">{{ $e->status }}</span>
                            </td>
                            <td class="px-5 py-3 text-right whitespace-nowrap">
                                <a href="{{ route('teacher.exams.questions', $e) }}" class="text-brand-600 hover:underline">Soal</a>
                                <a href="{{ route('teacher.grading.index', $e) }}" class="text-indigo-600 hover:underline ml-2">Grading</a>
                                <a href="{{ route('teacher.exams.edit', $e) }}" class="text-slate-600 hover:underline ml-2">Edit</a>
                                <form method="POST" action="{{ route('teacher.exams.destroy', $e) }}" class="inline" onsubmit="return confirm('Hapus ujian?')">
                                    @csrf @method('DELETE')
                                    <button class="text-rose-600 hover:underline ml-2">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-8 text-center text-slate-500">Belum ada ujian.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $exams->links() }}</div>
    </x-card>
</x-app-layout>
