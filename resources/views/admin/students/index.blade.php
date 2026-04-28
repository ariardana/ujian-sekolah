<x-app-layout>
    <x-slot name="header">
        <x-page-title title="Siswa" subtitle="Kelola data siswa">
            <x-btn variant="secondary" data-modal="import">Import Excel</x-btn>
            <x-btn variant="primary" :href="route('admin.students.create')">+ Tambah</x-btn>
        </x-page-title>
    </x-slot>

    <x-card>
        <form method="GET" class="mb-4 flex flex-wrap gap-2">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama / NISN..."
                class="block w-full max-w-sm rounded-lg border-slate-300 bg-white shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-slate-800 dark:border-slate-700">
            <select name="class_id"
                class="rounded-lg border-slate-300 bg-white shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-slate-800 dark:border-slate-700">
                <option value="">— Semua Kelas —</option>
                @foreach ($classes as $c)
                    <option value="{{ $c->id }}" @selected(request('class_id') == $c->id)>{{ $c->level }} {{ $c->name }} {{ $c->jurusan?->code }}</option>
                @endforeach
            </select>
            <x-btn type="submit" variant="secondary">Filter</x-btn>
        </form>

        <div class="overflow-x-auto -mx-5">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800/60">
                    <tr class="text-left text-xs font-semibold uppercase text-slate-500">
                        <th class="px-5 py-3">NISN</th>
                        <th class="px-5 py-3">Nama</th>
                        <th class="px-5 py-3">Kelas</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse ($students as $s)
                        <tr>
                            <td class="px-5 py-3 font-mono">{{ $s->nisn }}</td>
                            <td class="px-5 py-3 font-medium">{{ $s->name }}</td>
                            <td class="px-5 py-3 text-slate-500">
                                @if ($s->student?->schoolClass)
                                    {{ $s->student->schoolClass->level }} {{ $s->student->schoolClass->name }}
                                    {{ $s->student->schoolClass->jurusan?->code }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                @if ($s->is_active)
                                    <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">aktif</span>
                                @else
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-600 dark:bg-slate-800 dark:text-slate-300">nonaktif</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-right whitespace-nowrap">
                                <a href="{{ route('admin.students.edit', $s) }}" class="text-brand-600 hover:underline">Edit</a>
                                <form method="POST" action="{{ route('admin.students.reset', $s) }}" class="inline" onsubmit="return confirm('Reset password ke NISN?')">
                                    @csrf
                                    <button class="text-amber-600 hover:underline ml-2">Reset PW</button>
                                </form>
                                <form method="POST" action="{{ route('admin.students.destroy', $s) }}" class="inline" onsubmit="return confirm('Hapus siswa?')">
                                    @csrf @method('DELETE')
                                    <button class="text-rose-600 hover:underline ml-2">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-8 text-center text-slate-500">Belum ada siswa.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $students->links() }}</div>
    </x-card>

    <div x-data="{ open: false }" x-cloak
         x-init="document.querySelectorAll('[data-modal=import]').forEach(b => b.addEventListener('click', () => open = true))"
         x-show="open" class="fixed inset-0 z-50 grid place-items-center bg-slate-900/60 px-4">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 dark:bg-slate-900" @click.outside="open=false">
            <h3 class="text-lg font-semibold mb-4">Import Siswa dari Excel</h3>
            <form method="POST" action="{{ route('admin.students.import') }}" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <p class="text-xs text-slate-500">Header kolom yang didukung: <code class="font-mono">nisn, name, class_id, gender, phone</code>. Password default = NISN.</p>
                <input type="file" name="file" required accept=".xlsx,.xls,.csv"
                    class="block w-full text-sm">
                <select name="school_class_id" class="block w-full rounded-lg border-slate-300 dark:bg-slate-800 dark:border-slate-700">
                    <option value="">— Tanpa kelas default —</option>
                    @foreach ($classes as $c)
                        <option value="{{ $c->id }}">{{ $c->level }} {{ $c->name }} {{ $c->jurusan?->code }}</option>
                    @endforeach
                </select>
                <div class="flex gap-2 justify-end">
                    <x-btn variant="ghost" @click.prevent="open=false">Batal</x-btn>
                    <x-btn type="submit" variant="primary">Import</x-btn>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
