<x-app-layout>
    <x-slot name="header">
        <x-page-title title="Jurusan" subtitle="Kelola jurusan sekolah">
            <x-btn variant="primary" :href="route('admin.jurusans.create')">+ Tambah</x-btn>
        </x-page-title>
    </x-slot>

    <x-card>
        <form method="GET" class="mb-4">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari jurusan..."
                class="block w-full max-w-sm rounded-lg border-slate-300 bg-white shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-slate-800 dark:border-slate-700">
        </form>
        <div class="overflow-x-auto -mx-5">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800/60">
                    <tr class="text-left text-xs font-semibold uppercase text-slate-500">
                        <th class="px-5 py-3">Kode</th>
                        <th class="px-5 py-3">Nama</th>
                        <th class="px-5 py-3">Deskripsi</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse ($jurusans as $j)
                        <tr>
                            <td class="px-5 py-3 font-medium">{{ $j->code }}</td>
                            <td class="px-5 py-3">{{ $j->name }}</td>
                            <td class="px-5 py-3 text-slate-500">{{ $j->description }}</td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('admin.jurusans.edit', $j) }}" class="text-brand-600 hover:underline">Edit</a>
                                <form method="POST" action="{{ route('admin.jurusans.destroy', $j) }}" class="inline" onsubmit="return confirm('Hapus jurusan?')">
                                    @csrf @method('DELETE')
                                    <button class="text-rose-600 hover:underline ml-2">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-8 text-center text-slate-500">Belum ada jurusan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $jurusans->links() }}</div>
    </x-card>
</x-app-layout>
