<x-app-layout>
    <x-slot name="header">
        <x-page-title :title="$exam->exists ? 'Edit Ujian' : 'Buat Ujian Baru'" />
    </x-slot>

    <x-card>
        <form method="POST" action="{{ $exam->exists ? route('teacher.exams.update', $exam) : route('teacher.exams.store') }}" class="space-y-4 max-w-3xl">
            @csrf
            @if ($exam->exists) @method('PUT') @endif

            <x-form-input name="name" label="Nama Ujian" :value="$exam->name" />

            <div class="grid sm:grid-cols-2 gap-4">
                <x-form-select name="mapel_id" label="Mapel">
                    <option value="">— Pilih —</option>
                    @foreach ($mapels as $m)
                        <option value="{{ $m->id }}" @selected(old('mapel_id', $exam->mapel_id) == $m->id)>{{ $m->name }}</option>
                    @endforeach
                </x-form-select>
                <x-form-select name="school_class_id" label="Kelas">
                    <option value="">— Semua Kelas —</option>
                    @foreach ($classes as $c)
                        <option value="{{ $c->id }}" @selected(old('school_class_id', $exam->school_class_id) == $c->id)>
                            {{ $c->level }} {{ $c->name }} {{ $c->jurusan?->code }}
                        </option>
                    @endforeach
                </x-form-select>
            </div>

            <x-form-select name="semester_id" label="Semester">
                <option value="">— Tidak terkait semester —</option>
                @foreach ($semesters as $s)
                    <option value="{{ $s->id }}" @selected(old('semester_id', $exam->semester_id) == $s->id)>
                        {{ $s->academicYear?->year }} · Semester {{ $s->name }}
                    </option>
                @endforeach
            </x-form-select>

            <div class="grid sm:grid-cols-2 gap-4">
                <x-form-input name="start_at" label="Mulai" type="datetime-local"
                    :value="optional($exam->start_at)?->format('Y-m-d\TH:i')" />
                <x-form-input name="end_at" label="Selesai" type="datetime-local"
                    :value="optional($exam->end_at)?->format('Y-m-d\TH:i')" />
            </div>

            <div class="grid sm:grid-cols-3 gap-4">
                <x-form-input name="duration_minutes" label="Durasi (menit)" type="number" :value="$exam->duration_minutes" />
                <x-form-input name="passing_score" label="Nilai Lulus" type="number" :value="$exam->passing_score" />
                <x-form-input name="token" label="Token (opsional)" :value="$exam->token" />
            </div>

            <textarea name="description" rows="3" placeholder="Deskripsi / petunjuk ujian..."
                class="block w-full rounded-lg border-slate-300 bg-white shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-slate-800 dark:border-slate-700">{{ old('description', $exam->description) }}</textarea>

            <div class="grid sm:grid-cols-3 gap-3">
                <label class="inline-flex items-center gap-2 text-sm">
                    <input type="checkbox" name="randomize_questions" value="1" @checked(old('randomize_questions', $exam->randomize_questions))
                        class="rounded border-slate-300 text-brand-600 dark:border-slate-600 dark:bg-slate-800">
                    Acak urutan soal
                </label>
                <label class="inline-flex items-center gap-2 text-sm">
                    <input type="checkbox" name="randomize_options" value="1" @checked(old('randomize_options', $exam->randomize_options))
                        class="rounded border-slate-300 text-brand-600 dark:border-slate-600 dark:bg-slate-800">
                    Acak pilihan jawaban
                </label>
                <label class="inline-flex items-center gap-2 text-sm">
                    <input type="checkbox" name="show_result" value="1" @checked(old('show_result', $exam->show_result ?? true))
                        class="rounded border-slate-300 text-brand-600 dark:border-slate-600 dark:bg-slate-800">
                    Tampilkan nilai ke siswa
                </label>
            </div>

            <x-form-select name="status" label="Status">
                @foreach (['draft' => 'Draft', 'published' => 'Published', 'closed' => 'Closed'] as $k => $v)
                    <option value="{{ $k }}" @selected(old('status', $exam->status) === $k)>{{ $v }}</option>
                @endforeach
            </x-form-select>

            <div class="flex gap-2">
                <x-btn type="submit" variant="primary">Simpan</x-btn>
                <x-btn variant="secondary" :href="route('teacher.exams.index')">Batal</x-btn>
            </div>
        </form>
    </x-card>
</x-app-layout>
