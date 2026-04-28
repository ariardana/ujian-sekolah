<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Ujian Sekolah') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-slate-900 dark:text-slate-100">
    <div class="min-h-screen relative overflow-hidden flex items-center justify-center px-4 py-12
                bg-gradient-to-br from-brand-50 via-white to-slate-100
                dark:from-slate-950 dark:via-slate-900 dark:to-slate-900">
        <div class="absolute -top-32 -left-32 h-96 w-96 rounded-full bg-brand-200/40 blur-3xl dark:bg-brand-800/30"></div>
        <div class="absolute -bottom-32 -right-32 h-96 w-96 rounded-full bg-indigo-200/40 blur-3xl dark:bg-indigo-800/30"></div>

        <div class="relative z-10 w-full max-w-md">
            <div class="mb-6 flex flex-col items-center">
                <div class="grid place-items-center h-14 w-14 rounded-2xl bg-brand-600 text-white shadow-lg shadow-brand-600/30">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                    </svg>
                </div>
                <h1 class="mt-3 text-2xl font-bold tracking-tight">{{ config('app.name', 'Ujian Sekolah') }}</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Sistem Ujian Sekolah Online</p>
            </div>

            <div class="rounded-2xl bg-white/90 backdrop-blur shadow-xl ring-1 ring-slate-200 p-6 sm:p-8
                        dark:bg-slate-900/80 dark:ring-slate-800">
                {{ $slot }}
            </div>

            <p class="mt-6 text-center text-xs text-slate-500 dark:text-slate-400">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
