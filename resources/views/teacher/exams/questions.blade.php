<x-app-layout>
    <x-slot name="header">
        <x-page-title :title="'Soal Ujian: '.$exam->name" :subtitle="$exam->mapel?->name">
            @if ($exam->status === 'draft')
                <form method="POST" action="{{ route('teacher.exams.publish', $exam) }}">
                    @csrf
                    <x-btn variant="primary" type="submit">Publish</x-btn>
                </form>
            @elseif ($exam->status === 'published')
                <form method="POST" action="{{ route('teacher.exams.close', $exam) }}">
                    @csrf
                    <x-btn variant="danger" type="submit">Tutup</x-btn>
                </form>
            @endif
            <x-btn variant="secondary" :href="route('teacher.exams.edit', $exam)">Edit Detail</x-btn>
        </x-page-title>
    </x-slot>

    <div class="grid lg:grid-cols-2 gap-4">
        <x-card title="Soal Terdaftar ({{ $exam->questions->count() }})">
            <ol class="divide-y divide-slate-200 dark:divide-slate-800 -mx-5">
                @forelse ($exam->questions as $q)
                    <li class="px-5 py-3 flex items-start gap-3">
                        <span class="font-bold text-brand-600 w-6 shrink-0">{{ $loop->iteration }}.</span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm">{{ Str::limit(strip_tags($q->body), 140) }}</p>
                            <span class="text-xs text-slate-500">{{ $q->type === 'multiple_choice' ? 'PG' : 'Essay' }} · {{ $q->points }} poin</span>
                        </div>
                        <form method="POST" action="{{ route('teacher.exams.questions.detach', [$exam, $q]) }}">
                            @csrf @method('DELETE')
                            <button class="text-rose-600 hover:underline text-xs">Lepas</button>
                        </form>
                    </li>
                @empty
                    <li class="px-5 py-8 text-center text-sm text-slate-500">Belum ada soal di ujian ini.</li>
                @endforelse
            </ol>
        </x-card>

        <x-card title="Tambah Soal dari Bank ({{ $exam->mapel?->name }})">
            <form method="POST" action="{{ route('teacher.exams.questions.attach', $exam) }}">
                @csrf
                <ul class="divide-y divide-slate-200 dark:divide-slate-800 -mx-5">
                    @forelse ($available as $q)
                        <li class="px-5 py-3 flex items-start gap-3">
                            <input type="checkbox" name="question_ids[]" value="{{ $q->id }}"
                                class="mt-1 rounded border-slate-300 text-brand-600 dark:border-slate-600 dark:bg-slate-800">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm">{{ Str::limit(strip_tags($q->body), 140) }}</p>
                                <span class="text-xs text-slate-500">{{ $q->type === 'multiple_choice' ? 'PG' : 'Essay' }} · {{ $q->points }} poin</span>
                            </div>
                        </li>
                    @empty
                        <li class="px-5 py-8 text-center text-sm text-slate-500">Tidak ada soal tersedia.</li>
                    @endforelse
                </ul>
                @if ($available->isNotEmpty())
                    <div class="mt-4">
                        <x-btn type="submit" variant="primary">Tambahkan ke Ujian</x-btn>
                    </div>
                    <div class="mt-3">{{ $available->links() }}</div>
                @endif
            </form>
        </x-card>
    </div>
</x-app-layout>
