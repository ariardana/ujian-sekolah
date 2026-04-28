<x-app-layout>
    <x-slot name="header">
        <x-page-title title="Mata Pelajaran">
            <x-btn variant="primary" :href="route('admin.mapels.create')">+ Tambah</x-btn>
        </x-page-title>
    </x-slot>

    <x-card>
        <form method="GET" class="mb-4">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari mapel..."
                class="block w-full max-w-sm rounded-lg border-slate-300 bg-white shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-slate-800 dark:border-slate-700">
        </form>
        <div class="overflow-x-auto -mx-5">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800/60">
                    <tr class="text-left text-xs font-semibold uppercase text-slate-500">
                        <th class="px-5 py-3">Kode</th>
                        <th class="px-5 py-3">Nama</th>
                        <th class="px-5 py-3">Bab</th>
                        <th class="px-5 py-3">Soal</th>
                        <th class="px-5 py-3">Pengampu</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse ($mapels as $m)
                        <tr>
                            <td class="px-5 py-3 font-medium">{{ $m->code }}</td>
                            <td class="px-5 py-3">{{ $m->name }}</td>
                            <td class="px-5 py-3">{{ $m->chapters_count }}</td>
                            <td class="px-5 py-3">{{ $m->questions_count }}</td>
                            <td class="px-5 py-3 text-slate-500">{{ $m->teachers->pluck('name')->join(', ') ?: '-' }}</td>
                            <td class="px-5 py-3 text-right whitespace-nowrap">
                                <a href="{{ route('admin.mapels.edit', $m) }}" class="text-brand-600 hover:underline">Edit</a>
                                <form method="POST" action="{{ route('admin.mapels.destroy', $m) }}" class="inline" onsubmit="return confirm('Hapus mapel?')">
                                    @csrf @method('DELETE')
                                    <button class="text-rose-600 hover:underline ml-2">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-8 text-center text-slate-500">Belum ada mapel.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $mapels->links() }}</div>
    </x-card>
</x-app-layout>
