<x-app-layout>
    <x-slot name="header">
        <x-page-title :title="$class->exists ? 'Edit Kelas' : 'Tambah Kelas'" />
    </x-slot>

    <x-card>
        <form method="POST" action="{{ $class->exists ? route('admin.classes.update', $class) : route('admin.classes.store') }}" class="space-y-4 max-w-xl">
            @csrf
            @if ($class->exists) @method('PUT') @endif
            <div class="grid sm:grid-cols-2 gap-4">
                <x-form-select name="level" label="Tingkat">
                    @foreach (['X','XI','XII'] as $lvl)
                        <option value="{{ $lvl }}" @selected(old('level', $class->level) === $lvl)>{{ $lvl }}</option>
                    @endforeach
                </x-form-select>
                <x-form-input name="name" label="Nama Kelas" :value="$class->name" placeholder="1, 2, IPA-1" />
            </div>
            <x-form-select name="jurusan_id" label="Jurusan">
                <option value="">— Tanpa Jurusan —</option>
                @foreach ($jurusans as $j)
                    <option value="{{ $j->id }}" @selected(old('jurusan_id', $class->jurusan_id) == $j->id)>{{ $j->name }}</option>
                @endforeach
            </x-form-select>
            <x-form-input name="homeroom_teacher" label="Wali Kelas" :value="$class->homeroom_teacher" />

            <div class="flex gap-2">
                <x-btn type="submit" variant="primary">Simpan</x-btn>
                <x-btn variant="secondary" :href="route('admin.classes.index')">Batal</x-btn>
            </div>
        </form>
    </x-card>
</x-app-layout>
