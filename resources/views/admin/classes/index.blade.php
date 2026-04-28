<x-app-layout>
    <x-slot name="header">
        <x-page-title title="Kelas">
            <x-btn variant="primary" :href="route('admin.classes.create')">+ Tambah</x-btn>
        </x-page-title>
    </x-slot>

    <x-card>
        <form method="GET" class="mb-4">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari kelas..."
                class="block w-full max-w-sm rounded-lg border-slate-300 bg-white shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-slate-800 dark:border-slate-700">
        </form>
        <div class="overflow-x-auto -mx-5">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800/60">
                    <tr class="text-left text-xs font-semibold uppercase text-slate-500">
                        <th class="px-5 py-3">Kelas</th>
                        <th class="px-5 py-3">Jurusan</th>
                        <th class="px-5 py-3">Wali Kelas</th>
                        <th class="px-5 py-3">Siswa</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse ($classes as $c)
                        <tr>
                            <td class="px-5 py-3 font-medium">{{ $c->level }} {{ $c->name }}</td>
                            <td class="px-5 py-3">{{ $c->jurusan?->name ?? '-' }}</td>
                            <td class="px-5 py-3 text-slate-500">{{ $c->homeroom_teacher ?? '-' }}</td>
                            <td class="px-5 py-3">{{ $c->students_count }}</td>
                            <td class="px-5 py-3 text-right whitespace-nowrap">
                                <a href="{{ route('admin.classes.edit', $c) }}" class="text-brand-600 hover:underline">Edit</a>
                                <form method="POST" action="{{ route('admin.classes.destroy', $c) }}" class="inline" onsubmit="return confirm('Hapus kelas?')">
                                    @csrf @method('DELETE')
                                    <button class="text-rose-600 hover:underline ml-2">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-8 text-center text-slate-500">Belum ada kelas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $classes->links() }}</div>
    </x-card>
</x-app-layout>
