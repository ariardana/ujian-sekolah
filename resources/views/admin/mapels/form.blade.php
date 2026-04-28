<x-app-layout>
    <x-slot name="header">
        <x-page-title :title="$mapel->exists ? 'Edit Mapel' : 'Tambah Mapel'" />
    </x-slot>

    <x-card>
        <form method="POST" action="{{ $mapel->exists ? route('admin.mapels.update', $mapel) : route('admin.mapels.store') }}" class="space-y-4 max-w-2xl">
            @csrf
            @if ($mapel->exists) @method('PUT') @endif
            <div class="grid sm:grid-cols-2 gap-4">
                <x-form-input name="code" label="Kode" :value="$mapel->code" placeholder="MTK" />
                <x-form-input name="name" label="Nama" :value="$mapel->name" placeholder="Matematika" />
            </div>
            <x-form-input name="description" label="Deskripsi" :value="$mapel->description" />

            <div>
                <label class="block text-sm font-medium mb-2">Guru Pengampu</label>
                <div class="grid sm:grid-cols-2 gap-2 max-h-60 overflow-y-auto rounded-lg border border-slate-200 p-3 dark:border-slate-700">
                    @forelse ($teachers as $t)
                        <label class="inline-flex items-center gap-2 text-sm">
                            <input type="checkbox" name="teacher_ids[]" value="{{ $t->id }}"
                                @checked(in_array($t->id, old('teacher_ids', $mapel->teachers->pluck('id')->all())))
                                class="rounded border-slate-300 text-brand-600 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-800">
                            {{ $t->name }}
                        </label>
                    @empty
                        <p class="text-xs text-slate-500">Belum ada guru terdaftar.</p>
                    @endforelse
                </div>
            </div>

            <div class="flex gap-2">
                <x-btn type="submit" variant="primary">Simpan</x-btn>
                <x-btn variant="secondary" :href="route('admin.mapels.index')">Batal</x-btn>
            </div>
        </form>
    </x-card>
</x-app-layout>
