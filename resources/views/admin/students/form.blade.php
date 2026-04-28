<x-app-layout>
    <x-slot name="header">
        <x-page-title :title="$student->exists ? 'Edit Siswa' : 'Tambah Siswa'" />
    </x-slot>

    <x-card>
        <form method="POST" action="{{ $student->exists ? route('admin.students.update', $student) : route('admin.students.store') }}" class="space-y-4 max-w-2xl">
            @csrf
            @if ($student->exists) @method('PUT') @endif

            <div class="grid sm:grid-cols-2 gap-4">
                <x-form-input name="name" label="Nama" :value="$student->name" />
                <x-form-input name="nisn" label="NISN" :value="$student->nisn" />
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
                <x-form-select name="school_class_id" label="Kelas">
                    <option value="">— Pilih Kelas —</option>
                    @foreach ($classes as $c)
                        <option value="{{ $c->id }}" @selected(old('school_class_id', $student->student?->school_class_id) == $c->id)>
                            {{ $c->level }} {{ $c->name }} {{ $c->jurusan?->code }}
                        </option>
                    @endforeach
                </x-form-select>
                <x-form-select name="gender" label="Jenis Kelamin">
                    <option value="">— Pilih —</option>
                    <option value="L" @selected(old('gender', $student->student?->gender) === 'L')>Laki-laki</option>
                    <option value="P" @selected(old('gender', $student->student?->gender) === 'P')>Perempuan</option>
                </x-form-select>
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
                <x-form-input name="phone" label="Telepon" :value="$student->student?->phone" />
                <x-form-input name="birthdate" label="Tanggal Lahir" type="date" :value="optional($student->student?->birthdate)?->format('Y-m-d')" />
            </div>

            <x-form-input name="birthplace" label="Tempat Lahir" :value="$student->student?->birthplace" />
            <x-form-input name="address" label="Alamat" :value="$student->student?->address" />

            <x-form-input name="password" label="Password (kosongkan jika tidak ganti)" type="password" />
            @if ($student->exists)
                <label class="inline-flex items-center gap-2 text-sm">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $student->is_active))
                        class="rounded border-slate-300 text-brand-600 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-800">
                    Akun aktif
                </label>
            @endif

            <div class="flex gap-2">
                <x-btn type="submit" variant="primary">Simpan</x-btn>
                <x-btn variant="secondary" :href="route('admin.students.index')">Batal</x-btn>
            </div>
        </form>
    </x-card>
</x-app-layout>
