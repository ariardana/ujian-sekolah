<x-app-layout>
    <x-slot name="header">
        <x-page-title title="Bank Soal">
            <x-btn variant="primary" :href="route('teacher.questions.create')">+ Tambah Soal</x-btn>
        </x-page-title>
    </x-slot>

    <x-card>
        <form method="GET" class="mb-4 flex flex-wrap gap-2">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari pertanyaan..."
                class="block w-full max-w-sm rounded-lg border-slate-300 bg-white shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-slate-800 dark:border-slate-700">
            <select name="mapel_id" class="rounded-lg border-slate-300 bg-white shadow-sm dark:bg-slate-800 dark:border-slate-700">
                <option value="">— Semua Mapel —</option>
                @foreach ($mapels as $m)
                    <option value="{{ $m->id }}" @selected(request('mapel_id') == $m->id)>{{ $m->name }}</option>
                @endforeach
            </select>
            <select name="type" class="rounded-lg border-slate-300 bg-white shadow-sm dark:bg-slate-800 dark:border-slate-700">
                <option value="">— Semua Tipe —</option>
                <option value="multiple_choice" @selected(request('type') === 'multiple_choice')>Pilihan Ganda</option>
                <option value="essay" @selected(request('type') === 'essay')>Essay</option>
            </select>
            <x-btn type="submit" variant="secondary">Filter</x-btn>
        </form>

        <ul class="divide-y divide-slate-200 dark:divide-slate-800 -mx-5">
            @forelse ($questions as $q)
                <li class="px-5 py-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <span class="rounded-full bg-brand-100 px-2 py-0.5 text-xs text-brand-700 dark:bg-brand-900/40 dark:text-brand-300">{{ $q->mapel->name }}</span>
                                @if ($q->chapter)
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ $q->chapter->name }}</span>
                                @endif
                                <span class="rounded-full bg-indigo-100 px-2 py-0.5 text-xs text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300">
                                    {{ $q->type === 'multiple_choice' ? 'PG' : 'Essay' }}
                                </span>
                                <span class="text-xs text-slate-500">{{ $q->points }} poin · {{ $q->difficulty }}</span>
                            </div>
                            <p class="text-sm">{{ Str::limit(strip_tags($q->body), 180) }}</p>
                        </div>
                        <div class="flex gap-2 shrink-0">
                            <a href="{{ route('teacher.questions.edit', $q) }}" class="text-brand-600 hover:underline text-sm">Edit</a>
                            <form method="POST" action="{{ route('teacher.questions.destroy', $q) }}" onsubmit="return confirm('Hapus soal?')">
                                @csrf @method('DELETE')
                                <button class="text-rose-600 hover:underline text-sm">Hapus</button>
                            </form>
                        </div>
                    </div>
                </li>
            @empty
                <li class="px-5 py-8 text-center text-sm text-slate-500">Belum ada soal.</li>
            @endforelse
        </ul>
        <div class="mt-3">{{ $questions->links() }}</div>
    </x-card>
</x-app-layout>
