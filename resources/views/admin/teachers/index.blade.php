<x-app-layout>
    <x-slot name="header">
        <x-page-title title="Guru">
            <x-btn variant="primary" :href="route('admin.teachers.create')">+ Tambah</x-btn>
        </x-page-title>
    </x-slot>

    <x-card>
        <form method="GET" class="mb-4">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama / email..."
                class="block w-full max-w-sm rounded-lg border-slate-300 bg-white shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-slate-800 dark:border-slate-700">
        </form>
        <div class="overflow-x-auto -mx-5">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800/60">
                    <tr class="text-left text-xs font-semibold uppercase text-slate-500">
                        <th class="px-5 py-3">Nama</th>
                        <th class="px-5 py-3">Email</th>
                        <th class="px-5 py-3">NIP</th>
                        <th class="px-5 py-3">Mapel</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse ($teachers as $t)
                        <tr>
                            <td class="px-5 py-3 font-medium">{{ $t->name }}</td>
                            <td class="px-5 py-3">{{ $t->email }}</td>
                            <td class="px-5 py-3 font-mono">{{ $t->teacher?->nip ?? '-' }}</td>
                            <td class="px-5 py-3 text-slate-500">{{ $t->mapels->pluck('name')->join(', ') ?: '-' }}</td>
                            <td class="px-5 py-3 text-right whitespace-nowrap">
                                <a href="{{ route('admin.teachers.edit', $t) }}" class="text-brand-600 hover:underline">Edit</a>
                                <form method="POST" action="{{ route('admin.teachers.reset', $t) }}" class="inline" onsubmit="return confirm('Reset password ke \'password\'?')">
                                    @csrf
                                    <button class="text-amber-600 hover:underline ml-2">Reset PW</button>
                                </form>
                                <form method="POST" action="{{ route('admin.teachers.destroy', $t) }}" class="inline" onsubmit="return confirm('Hapus guru?')">
                                    @csrf @method('DELETE')
                                    <button class="text-rose-600 hover:underline ml-2">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-8 text-center text-slate-500">Belum ada guru.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $teachers->links() }}</div>
    </x-card>
</x-app-layout>
