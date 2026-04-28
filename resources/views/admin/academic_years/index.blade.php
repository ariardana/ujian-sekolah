<x-app-layout>
    <x-slot name="header">
        <x-page-title title="Tahun Ajaran & Semester">
            <x-btn variant="primary" :href="route('admin.academic-years.create')">+ Tambah</x-btn>
        </x-page-title>
    </x-slot>

    <div class="grid gap-4">
        @forelse ($years as $y)
            <x-card>
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h3 class="text-lg font-semibold">{{ $y->year }}</h3>
                        @if ($y->is_active)
                            <span class="inline-block mt-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">AKTIF</span>
                        @endif
                    </div>
                    <div class="flex gap-2">
                        <x-btn variant="secondary" :href="route('admin.academic-years.edit', $y)">Edit</x-btn>
                        <form method="POST" action="{{ route('admin.academic-years.destroy', $y) }}" onsubmit="return confirm('Hapus tahun ajaran?')">
                            @csrf @method('DELETE')
                            <x-btn variant="danger" type="submit">Hapus</x-btn>
                        </form>
                    </div>
                </div>
                <div class="mt-4 grid sm:grid-cols-2 gap-3">
                    @foreach ($y->semesters as $s)
                        <div class="flex items-center justify-between rounded-lg border border-slate-200 px-4 py-3 dark:border-slate-700">
                            <div>
                                <p class="font-medium">Semester {{ $s->name }}</p>
                                @if ($s->is_active)
                                    <span class="text-xs text-emerald-600 dark:text-emerald-400">Aktif</span>
                                @else
                                    <span class="text-xs text-slate-500">Tidak aktif</span>
                                @endif
                            </div>
                            <form method="POST" action="{{ route('admin.semesters.toggle', $s) }}">
                                @csrf
                                <x-btn variant="ghost" type="submit">{{ $s->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</x-btn>
                            </form>
                        </div>
                    @endforeach
                </div>
            </x-card>
        @empty
            <x-card>
                <p class="text-center text-slate-500 py-8">Belum ada tahun ajaran.</p>
            </x-card>
        @endforelse
    </div>
    <div class="mt-4">{{ $years->links() }}</div>
</x-app-layout>
