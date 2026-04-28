<x-app-layout>
    <x-slot name="header">
        <x-page-title title="Bab Mapel" subtitle="Kelola bab/kategori soal per mapel" />
    </x-slot>

    <div class="grid lg:grid-cols-3 gap-4">
        <x-card title="Tambah Bab" class="lg:col-span-1">
            <form method="POST" action="{{ route('teacher.chapters.store') }}" class="space-y-3">
                @csrf
                <x-form-select name="mapel_id" label="Mapel">
                    @foreach ($mapels as $m)
                        <option value="{{ $m->id }}">{{ $m->name }}</option>
                    @endforeach
                </x-form-select>
                <x-form-input name="name" label="Nama Bab" placeholder="Bab 1: Pengantar" />
                <x-form-input name="order" label="Urutan" type="number" value="0" />
                <x-btn type="submit" variant="primary">Tambah</x-btn>
            </form>
        </x-card>

        <x-card title="Daftar Bab" class="lg:col-span-2">
            <ul class="divide-y divide-slate-200 dark:divide-slate-800 -mx-5">
                @forelse ($chapters as $c)
                    <li class="flex items-center justify-between px-5 py-3">
                        <div>
                            <p class="font-medium">{{ $c->name }}</p>
                            <p class="text-xs text-slate-500">{{ $c->mapel->name }} · urutan {{ $c->order }}</p>
                        </div>
                        <form method="POST" action="{{ route('teacher.chapters.destroy', $c) }}" onsubmit="return confirm('Hapus bab?')">
                            @csrf @method('DELETE')
                            <button class="text-rose-600 hover:underline text-sm">Hapus</button>
                        </form>
                    </li>
                @empty
                    <li class="px-5 py-6 text-center text-sm text-slate-500">Belum ada bab.</li>
                @endforelse
            </ul>
            <div class="mt-3">{{ $chapters->links() }}</div>
        </x-card>
    </div>
</x-app-layout>
