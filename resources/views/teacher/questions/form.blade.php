<x-app-layout>
    <x-slot name="header">
        <x-page-title :title="$question->exists ? 'Edit Soal' : 'Tambah Soal'" />
    </x-slot>

    <x-card>
        <form method="POST" enctype="multipart/form-data"
              action="{{ $question->exists ? route('teacher.questions.update', $question) : route('teacher.questions.store') }}"
              class="space-y-4 max-w-3xl"
              x-data="{ type: '{{ old('type', $question->type) }}' }">
            @csrf
            @if ($question->exists) @method('PUT') @endif

            <div class="grid sm:grid-cols-3 gap-4">
                <x-form-select name="mapel_id" label="Mapel">
                    @foreach ($mapels as $m)
                        <option value="{{ $m->id }}" @selected(old('mapel_id', $question->mapel_id) == $m->id)>{{ $m->name }}</option>
                    @endforeach
                </x-form-select>
                <x-form-select name="chapter_id" label="Bab">
                    <option value="">— Tanpa Bab —</option>
                    @foreach ($chapters as $c)
                        <option value="{{ $c->id }}" data-mapel="{{ $c->mapel_id }}"
                            @selected(old('chapter_id', $question->chapter_id) == $c->id)>{{ $c->name }}</option>
                    @endforeach
                </x-form-select>
                <x-form-select name="type" label="Tipe Soal" x-model="type">
                    <option value="multiple_choice">Pilihan Ganda</option>
                    <option value="essay">Essay</option>
                </x-form-select>
            </div>

            <div class="grid sm:grid-cols-3 gap-4">
                <x-form-input name="points" label="Poin" type="number" :value="$question->points ?? 1" />
                <x-form-select name="difficulty" label="Tingkat Kesulitan">
                    @foreach (['easy' => 'Mudah', 'medium' => 'Sedang', 'hard' => 'Sulit'] as $k => $v)
                        <option value="{{ $k }}" @selected(old('difficulty', $question->difficulty) === $k)>{{ $v }}</option>
                    @endforeach
                </x-form-select>
                <div>
                    <label class="block text-sm font-medium mb-1">Gambar (opsional)</label>
                    <input type="file" name="image" accept="image/*" class="block w-full text-sm">
                    @if ($question->image)
                        <img src="{{ asset('storage/'.$question->image) }}" class="mt-2 max-h-24 rounded border">
                    @endif
                </div>
            </div>

            <div>
                <label for="body" class="block text-sm font-medium mb-1">Pertanyaan</label>
                <textarea id="body" name="body" rows="4"
                    class="block w-full rounded-lg border-slate-300 bg-white shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-slate-800 dark:border-slate-700">{{ old('body', $question->body) }}</textarea>
                <x-input-error :messages="$errors->get('body')" class="mt-2" />
            </div>

            <div x-show="type === 'multiple_choice'" class="space-y-3">
                <label class="block text-sm font-medium">Pilihan Jawaban (pilih jawaban benar)</label>
                @php
                    $existing = $question->options->values();
                    $rows = old('options', $existing->map(fn($o) => ['body' => $o->body])->toArray());
                    if (empty($rows)) {
                        $rows = [['body' => ''], ['body' => ''], ['body' => ''], ['body' => '']];
                    }
                    $correctIdx = old('correct', (string) $existing->search(fn($o) => $o->is_correct));
                @endphp
                @foreach ($rows as $i => $opt)
                    <div class="flex items-center gap-2">
                        <span class="w-6 text-center font-bold">{{ chr(65 + $i) }}</span>
                        <input type="radio" name="correct" value="{{ $i }}" @checked((string)$correctIdx === (string)$i)
                            class="text-brand-600 focus:ring-brand-500">
                        <input type="text" name="options[{{ $i }}][body]" value="{{ $opt['body'] ?? '' }}"
                            class="block w-full rounded-lg border-slate-300 bg-white shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-slate-800 dark:border-slate-700">
                    </div>
                @endforeach
                <p class="text-xs text-slate-500">Tandai radio button di samping pilihan untuk menentukan jawaban benar.</p>
            </div>

            <div class="flex gap-2">
                <x-btn type="submit" variant="primary">Simpan</x-btn>
                <x-btn variant="secondary" :href="route('teacher.questions.index')">Batal</x-btn>
            </div>
        </form>
    </x-card>
</x-app-layout>
