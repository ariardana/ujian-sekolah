<x-app-layout>
    <x-slot name="header">
        <x-page-title :title="'Nilai: '.$participant->user->name" :subtitle="$exam->name" />
    </x-slot>

    <div class="grid lg:grid-cols-3 gap-4">
        <x-card title="Ringkasan" class="lg:col-span-1">
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-slate-500">Siswa</dt><dd class="font-medium">{{ $participant->user->name }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">NISN</dt><dd class="font-mono">{{ $participant->user->nisn }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Status</dt><dd>{{ $participant->status }}</dd></div>
                @if ($participant->result)
                    <div class="flex justify-between"><dt class="text-slate-500">Nilai</dt>
                        <dd class="font-bold text-lg">{{ $participant->result->total_score }} / {{ $participant->result->max_score }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Persentase</dt>
                        <dd>{{ number_format($participant->result->percentage, 1) }}%</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Lulus</dt>
                        <dd>{{ $participant->result->is_passed ? '✓ Lulus' : '✗ Belum' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Benar / Salah / Kosong</dt>
                        <dd>{{ $participant->result->correct_count }} / {{ $participant->result->wrong_count }} / {{ $participant->result->unanswered_count }}</dd></div>
                @endif
            </dl>
        </x-card>

        <div class="lg:col-span-2 space-y-4">
            @foreach ($participant->answers as $i => $a)
                <x-card>
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <div>
                            <span class="font-semibold">Soal {{ $i + 1 }}</span>
                            <span class="text-xs text-slate-500">— {{ $a->question->type === 'multiple_choice' ? 'PG' : 'Essay' }} · {{ $a->question->points }} poin</span>
                        </div>
                        @if ($a->is_correct === true)
                            <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">Benar</span>
                        @elseif ($a->is_correct === false)
                            <span class="rounded-full bg-rose-100 px-2 py-0.5 text-xs text-rose-700 dark:bg-rose-900/40 dark:text-rose-300">Salah</span>
                        @else
                            <span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">Belum dinilai</span>
                        @endif
                    </div>
                    <p class="text-sm">{{ $a->question->body }}</p>

                    @if ($a->question->type === 'multiple_choice')
                        <ul class="mt-3 space-y-1 text-sm">
                            @foreach ($a->question->options as $opt)
                                <li class="flex items-start gap-2 rounded-lg p-2
                                    @class([
                                        'bg-emerald-50 dark:bg-emerald-900/20' => $opt->is_correct,
                                        'bg-rose-50 dark:bg-rose-900/20' => $a->selected_option_id === $opt->id && !$opt->is_correct,
                                    ])">
                                    <span class="font-bold w-6">{{ $opt->label }}.</span>
                                    <span>{{ $opt->body }}</span>
                                    @if ($a->selected_option_id === $opt->id)
                                        <span class="ml-auto text-xs text-slate-500">(jawaban siswa)</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="mt-3 rounded-lg bg-slate-50 p-3 text-sm dark:bg-slate-800/60">
                            <p class="text-xs text-slate-500 mb-1">Jawaban siswa:</p>
                            <p class="whitespace-pre-wrap">{{ $a->answer_text ?: '— Tidak menjawab —' }}</p>
                        </div>
                        <form method="POST" action="{{ route('teacher.grading.essay', [$exam, $participant, $a]) }}"
                              class="mt-3 grid sm:grid-cols-3 gap-2">
                            @csrf
                            <input type="number" name="score" min="0" max="{{ $a->question->points }}" step="0.5" value="{{ $a->score }}"
                                placeholder="Skor (0–{{ $a->question->points }})"
                                class="rounded-lg border-slate-300 dark:bg-slate-800 dark:border-slate-700" required>
                            <input type="text" name="teacher_note" value="{{ $a->teacher_note }}" placeholder="Catatan (opsional)"
                                class="rounded-lg border-slate-300 sm:col-span-1 dark:bg-slate-800 dark:border-slate-700">
                            <x-btn type="submit" variant="primary">Simpan Nilai</x-btn>
                        </form>
                    @endif
                </x-card>
            @endforeach
        </div>
    </div>
</x-app-layout>
