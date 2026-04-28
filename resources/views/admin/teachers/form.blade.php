<x-app-layout>
    <x-slot name="header">
        <x-page-title :title="$teacher->exists ? 'Edit Guru' : 'Tambah Guru'" />
    </x-slot>

    <x-card>
        <form method="POST" action="{{ $teacher->exists ? route('admin.teachers.update', $teacher) : route('admin.teachers.store') }}" class="space-y-4 max-w-2xl">
            @csrf
            @if ($teacher->exists) @method('PUT') @endif

            <div class="grid sm:grid-cols-2 gap-4">
                <x-form-input name="name" label="Nama" :value="$teacher->name" />
                <x-form-input name="email" label="Email" :value="$teacher->email" type="email" />
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
                <x-form-input name="nip" label="NIP" :value="$teacher->teacher?->nip" />
                <x-form-select name="gender" label="Jenis Kelamin">
                    <option value="">— Pilih —</option>
                    <option value="L" @selected(old('gender', $teacher->teacher?->gender) === 'L')>Laki-laki</option>
                    <option value="P" @selected(old('gender', $teacher->teacher?->gender) === 'P')>Perempuan</option>
                </x-form-select>
            </div>

            <x-form-input name="phone" label="Telepon" :value="$teacher->teacher?->phone" />
            <x-form-input name="address" label="Alamat" :value="$teacher->teacher?->address" />

            <div>
                <label class="block text-sm font-medium mb-2">Mapel Diampu</label>
                <div class="grid sm:grid-cols-2 gap-2 max-h-60 overflow-y-auto rounded-lg border border-slate-200 p-3 dark:border-slate-700">
                    @forelse ($mapels as $m)
                        <label class="inline-flex items-center gap-2 text-sm">
                            <input type="checkbox" name="mapel_ids[]" value="{{ $m->id }}"
                                @checked(in_array($m->id, old('mapel_ids', $teacher->mapels->pluck('id')->all())))
                                class="rounded border-slate-300 text-brand-600 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-800">
                            {{ $m->name }}
                        </label>
                    @empty
                        <p class="text-xs text-slate-500">Belum ada mapel.</p>
                    @endforelse
                </div>
            </div>

            <x-form-input name="password" label="Password (kosongkan jika tidak ganti)" type="password" />
            @if ($teacher->exists)
                <label class="inline-flex items-center gap-2 text-sm">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $teacher->is_active))
                        class="rounded border-slate-300 text-brand-600 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-800">
                    Akun aktif
                </label>
            @endif

            <div class="flex gap-2">
                <x-btn type="submit" variant="primary">Simpan</x-btn>
                <x-btn variant="secondary" :href="route('admin.teachers.index')">Batal</x-btn>
            </div>
        </form>
    </x-card>
</x-app-layout>
