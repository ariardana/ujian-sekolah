<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h1 class="text-xl font-bold">{{ $exam->name }}</h1>
                <p class="text-xs text-slate-500">{{ $exam->mapel?->name }}</p>
            </div>
            <div x-data="examTimer({{ $deadline->getTimestamp() * 1000 }})" x-init="start()"
                 class="rounded-xl bg-slate-900 px-4 py-2 text-white shadow-lg">
                <p class="text-[10px] uppercase tracking-widest text-slate-400">Sisa Waktu</p>
                <p class="font-mono text-2xl font-semibold tabular-nums" x-text="display"></p>
            </div>
        </div>
    </x-slot>

    @php
        $current = $current;
        $isFlagged = (bool) ($current?->is_flagged ?? false);
    @endphp

    <div class="grid lg:grid-cols-[1fr_280px] gap-4" x-data="examTake({
        examId: {{ $exam->id }},
        questionId: {{ $question->id }},
        saveUrl: '{{ route('student.exams.answer', [$exam, $question]) }}',
        submitUrl: '{{ route('student.exams.submit', $exam) }}',
        initialSelected: {{ $current?->selected_option_id ? (int)$current->selected_option_id : 'null' }},
        initialText: @js($current?->answer_text ?? ''),
        initialFlagged: {{ $isFlagged ? 'true' : 'false' }},
    })">
        <x-card>
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm text-slate-500">Soal {{ $number }} dari {{ $total }}</p>
                <label class="inline-flex items-center gap-2 text-sm cursor-pointer">
                    <input type="checkbox" x-model="flagged" @change="autosave()"
                        class="rounded border-slate-300 text-amber-500 focus:ring-amber-400 dark:border-slate-600 dark:bg-slate-800">
                    <span class="text-amber-600 dark:text-amber-400">Tandai ragu</span>
                </label>
            </div>

            <div class="max-w-none">
                <p class="whitespace-pre-wrap font-medium text-base">{{ $question->body }}</p>
                @if ($question->image)
                    <img src="{{ asset('storage/'.$question->image) }}" alt="Gambar soal" class="mt-3 max-h-80 rounded-lg border">
                @endif
            </div>

            @if ($question->type === 'multiple_choice')
                <div class="mt-5 space-y-2">
                    @foreach ($question->options as $opt)
                        <label class="flex items-start gap-3 rounded-xl border-2 px-4 py-3 cursor-pointer transition
                                       hover:border-brand-400 dark:hover:border-brand-500"
                               :class="selected === {{ $opt->id }} ? 'border-brand-500 bg-brand-50 dark:bg-brand-900/30' : 'border-slate-200 dark:border-slate-700'">
                            <input type="radio" value="{{ $opt->id }}" x-model.number="selected" @change="autosave()"
                                class="mt-1 text-brand-600 focus:ring-brand-500">
                            <div>
                                <span class="font-bold text-brand-600 dark:text-brand-400">{{ $opt->label }}.</span>
                                <span>{{ $opt->body }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            @else
                <div class="mt-5">
                    <textarea rows="8" x-model="text" @input.debounce.800ms="autosave()"
                        placeholder="Tulis jawaban Anda di sini..."
                        class="block w-full rounded-lg border-slate-300 bg-white shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-slate-800 dark:border-slate-700"></textarea>
                </div>
            @endif

            <div class="mt-6 flex items-center justify-between">
                @if ($number > 1)
                    <x-btn variant="secondary" :href="route('student.exams.take', [$exam, $number - 1])">← Sebelumnya</x-btn>
                @else
                    <span></span>
                @endif

                <div class="flex items-center gap-2">
                    <span class="text-xs text-slate-500" x-text="status"></span>
                    @if ($number < $total)
                        <x-btn variant="primary" :href="route('student.exams.take', [$exam, $number + 1])">Berikutnya →</x-btn>
                    @else
                        <x-btn variant="danger" @click.prevent="confirmSubmit()">Selesai &amp; Submit</x-btn>
                    @endif
                </div>
            </div>
        </x-card>

        <x-card title="Navigasi Soal">
            <div class="grid grid-cols-5 gap-2">
                @foreach ($order as $i => $qid)
                    @php
                        $a = $answers->get($qid);
                        $answered = $a && ($a->selected_option_id !== null || filled($a->answer_text));
                        $flag = $a && $a->is_flagged;
                        $active = ($i + 1) === $number;
                    @endphp
                    <a href="{{ route('student.exams.take', [$exam, $i + 1]) }}"
                       class="aspect-square grid place-items-center rounded-lg text-sm font-semibold border-2 transition relative
                              @class([
                                  'border-brand-600 bg-brand-600 text-white' => $active,
                                  'border-emerald-500 bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' => !$active && $answered && !$flag,
                                  'border-amber-500 bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300' => !$active && $flag,
                                  'border-slate-200 hover:border-brand-300 dark:border-slate-700' => !$active && !$answered && !$flag,
                              ])">
                        {{ $i + 1 }}
                    </a>
                @endforeach
            </div>
            <div class="mt-4 space-y-1 text-xs text-slate-500">
                <p><span class="inline-block h-3 w-3 rounded-sm bg-brand-600 align-middle"></span> Saat ini</p>
                <p><span class="inline-block h-3 w-3 rounded-sm border-2 border-emerald-500 bg-emerald-50 align-middle"></span> Sudah dijawab</p>
                <p><span class="inline-block h-3 w-3 rounded-sm border-2 border-amber-500 bg-amber-50 align-middle"></span> Ditandai ragu</p>
                <p><span class="inline-block h-3 w-3 rounded-sm border-2 border-slate-300 align-middle"></span> Belum dijawab</p>
            </div>

            <form method="POST" action="{{ route('student.exams.submit', $exam) }}" id="exam-submit-form" class="mt-4">
                @csrf
                <x-btn variant="danger" type="submit" class="w-full justify-center" @click.prevent="confirmSubmit()">
                    Submit Ujian
                </x-btn>
            </form>
        </x-card>
    </div>

    @push('scripts')
    <script>
        function examTimer(deadlineMs) {
            return {
                display: '00:00:00',
                _interval: null,
                start() {
                    const tick = () => {
                        const diff = Math.max(0, Math.floor((deadlineMs - Date.now()) / 1000));
                        const h = String(Math.floor(diff / 3600)).padStart(2, '0');
                        const m = String(Math.floor((diff % 3600) / 60)).padStart(2, '0');
                        const s = String(diff % 60).padStart(2, '0');
                        this.display = `${h}:${m}:${s}`;
                        if (diff <= 0) {
                            clearInterval(this._interval);
                            document.getElementById('exam-submit-form')?.submit();
                        }
                    };
                    tick();
                    this._interval = setInterval(tick, 1000);
                }
            };
        }
        function examTake(cfg) {
            return {
                selected: cfg.initialSelected,
                text: cfg.initialText,
                flagged: cfg.initialFlagged,
                status: '',
                _saveTimer: null,
                autosave() {
                    clearTimeout(this._saveTimer);
                    this.status = 'Menyimpan...';
                    this._saveTimer = setTimeout(async () => {
                        try {
                            const res = await fetch(cfg.saveUrl, {
                                method: 'POST',
                                credentials: 'same-origin',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                },
                                body: JSON.stringify({
                                    selected_option_id: this.selected,
                                    answer_text: this.text,
                                    is_flagged: this.flagged ? 1 : 0,
                                }),
                            });
                            const data = await res.json();
                            this.status = data.ok ? 'Tersimpan ✓' : (data.message || 'Gagal menyimpan');
                            if (!data.ok && /habis|tidak aktif/i.test(data.message)) {
                                document.getElementById('exam-submit-form')?.submit();
                            }
                        } catch {
                            this.status = 'Offline – akan dicoba lagi';
                        }
                    }, 250);
                },
                confirmSubmit() {
                    if (confirm('Yakin submit ujian? Jawaban tidak bisa diubah lagi.')) {
                        document.getElementById('exam-submit-form').submit();
                    }
                },
            };
        }
    </script>
    @endpush
</x-app-layout>
