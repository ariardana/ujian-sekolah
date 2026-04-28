<x-guest-layout>
    <h2 class="text-xl font-semibold text-center mb-1">Selamat Datang</h2>
    <p class="text-sm text-slate-500 dark:text-slate-400 text-center mb-6">Masuk untuk melanjutkan</p>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-emerald-50 px-4 py-2 text-sm text-emerald-700 ring-1 ring-emerald-200 dark:bg-emerald-900/40 dark:text-emerald-200 dark:ring-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label for="identifier" class="block text-sm font-medium mb-1">Identitas</label>
            <input id="identifier" name="identifier" type="text" required autofocus
                value="{{ old('identifier') }}"
                placeholder="Email guru / NISN siswa / Username admin"
                class="block w-full rounded-lg border-slate-300 bg-white shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100">
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                Guru gunakan email · Siswa gunakan NISN · Admin gunakan username
            </p>
            <x-input-error :messages="$errors->get('identifier')" class="mt-2" />
        </div>

        <div>
            <label for="password" class="block text-sm font-medium mb-1">Kata Sandi</label>
            <input id="password" name="password" type="password" required autocomplete="current-password"
                class="block w-full rounded-lg border-slate-300 bg-white shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between">
            <label class="inline-flex items-center text-sm">
                <input type="checkbox" name="remember"
                    class="rounded border-slate-300 text-brand-600 shadow-sm focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-800">
                <span class="ml-2 text-slate-600 dark:text-slate-300">Ingat saya</span>
            </label>
        </div>

        <button type="submit"
            class="w-full inline-flex justify-center items-center gap-2 rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-brand-600/30 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 transition">
            Masuk
        </button>
    </form>
</x-guest-layout>
