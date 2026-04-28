<x-app-layout>
    <x-slot name="header">
        <x-page-title :title="$exam->name" :subtitle="$exam->mapel?->name" />
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <x-card>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-slate-500">Mapel</dt><dd class="font-medium">{{ $exam->mapel?->name }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Mulai</dt><dd>{{ $exam->start_at?->format('d M Y H:i') }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Selesai</dt><dd>{{ $exam->end_at?->format('d M Y H:i') }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Durasi</dt><dd>{{ $exam->duration_minutes }} menit</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Jumlah Soal</dt><dd>{{ $exam->questions()->count() }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Nilai Lulus</dt><dd>{{ $exam->passing_score }}</dd></div>
            </dl>

            @if ($exam->description)
                <div class="mt-4 rounded-lg bg-slate-50 p-3 text-sm dark:bg-slate-800/60">
                    <p class="font-medium mb-1">Petunjuk:</p>
                    <p class="whitespace-pre-wrap">{{ $exam->description }}</p>
                </div>
            @endif

            <div class="mt-6 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800 dark:border-amber-800/40 dark:bg-amber-900/20 dark:text-amber-300">
                <p class="font-medium mb-1">Perhatian</p>
                <ul class="list-disc list-inside space-y-1">
                    <li>Pastikan koneksi internet stabil sebelum memulai.</li>
                    <li>Timer akan berjalan setelah Anda mengklik tombol mulai dan tidak dapat dijeda.</li>
                    <li>Jawaban tersimpan otomatis (autosave).</li>
                    <li>Tutup tab/keluar tidak menghentikan timer; selesaikan tepat waktu.</li>
                </ul>
            </div>

            @if ($participant && in_array($participant->status, ['submitted', 'graded']))
                <div class="mt-6">
                    <x-btn variant="primary" :href="route('student.exams.result', $exam)">Lihat Hasil</x-btn>
                </div>
            @else
                <form method="POST" action="{{ route('student.exams.start', $exam) }}" class="mt-6 space-y-3">
                    @csrf
                    @if ($exam->token)
                        <x-form-input name="token" label="Token Ujian" placeholder="Masukkan token dari pengawas" />
                    @endif
                    <x-btn variant="primary" type="submit">
                        {{ $participant?->status === 'in_progress' ? 'Lanjutkan Ujian' : 'Mulai Ujian Sekarang' }}
                    </x-btn>
                </form>
            @endif
        </x-card>
    </div>
</x-app-layout>
