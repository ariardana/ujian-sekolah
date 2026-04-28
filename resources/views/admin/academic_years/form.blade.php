<x-app-layout>
    <x-slot name="header">
        <x-page-title :title="$year->exists ? 'Edit Tahun Ajaran' : 'Tambah Tahun Ajaran'" />
    </x-slot>

    <x-card>
        <form method="POST" action="{{ $year->exists ? route('admin.academic-years.update', $year) : route('admin.academic-years.store') }}" class="space-y-4 max-w-xl">
            @csrf
            @if ($year->exists) @method('PUT') @endif
            <x-form-input name="year" label="Tahun Ajaran" :value="$year->year" placeholder="2024/2025" />
            <label class="inline-flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $year->is_active))
                    class="rounded border-slate-300 text-brand-600 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-800">
                Jadikan tahun ajaran aktif
            </label>
            <div class="flex gap-2">
                <x-btn type="submit" variant="primary">Simpan</x-btn>
                <x-btn variant="secondary" :href="route('admin.academic-years.index')">Batal</x-btn>
            </div>
        </form>
    </x-card>
</x-app-layout>
