<x-app-layout>
    <x-slot name="header">
        <x-page-title :title="$jurusan->exists ? 'Edit Jurusan' : 'Tambah Jurusan'" />
    </x-slot>

    <x-card>
        <form method="POST" action="{{ $jurusan->exists ? route('admin.jurusans.update', $jurusan) : route('admin.jurusans.store') }}" class="space-y-4 max-w-xl">
            @csrf
            @if ($jurusan->exists) @method('PUT') @endif
            <x-form-input name="code" label="Kode" :value="$jurusan->code" placeholder="RPL" />
            <x-form-input name="name" label="Nama" :value="$jurusan->name" placeholder="Rekayasa Perangkat Lunak" />
            <x-form-input name="description" label="Deskripsi" :value="$jurusan->description" />
            <div class="flex gap-2">
                <x-btn type="submit" variant="primary">Simpan</x-btn>
                <x-btn variant="secondary" :href="route('admin.jurusans.index')">Batal</x-btn>
            </div>
        </form>
    </x-card>
</x-app-layout>
