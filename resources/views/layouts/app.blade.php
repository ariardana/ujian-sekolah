<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'Ujian Sekolah') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="h-full font-sans antialiased bg-slate-50 text-slate-800 dark:bg-slate-950 dark:text-slate-100"
      x-data="{ sidebarOpen: false }">

    @include('layouts.sidebar')

    <div class="lg:pl-64 min-h-screen flex flex-col">
        @include('layouts.topbar')

        @isset($header)
            <header class="bg-white/60 backdrop-blur border-b border-slate-200 dark:bg-slate-900/60 dark:border-slate-800">
                <div class="px-4 sm:px-6 lg:px-8 py-5">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <main class="flex-1 px-4 sm:px-6 lg:px-8 py-6">
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                     class="mb-4 flex items-start gap-3 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-800 ring-1 ring-emerald-200
                            dark:bg-emerald-900/40 dark:text-emerald-200 dark:ring-emerald-800">
                    <svg class="h-5 w-5 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                    </svg>
                    <span class="flex-1">{{ session('success') }}</span>
                    <button type="button" @click="show = false" class="text-emerald-700/70 hover:text-emerald-700">×</button>
                </div>
            @endif

            @if ($errors->any() && ! request()->routeIs('login'))
                <div class="mb-4 rounded-lg bg-rose-50 px-4 py-3 text-sm text-rose-800 ring-1 ring-rose-200
                            dark:bg-rose-900/40 dark:text-rose-200 dark:ring-rose-800">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{ $slot }}
        </main>

        <footer class="border-t border-slate-200 dark:border-slate-800 px-4 sm:px-6 lg:px-8 py-4 text-xs text-slate-500 dark:text-slate-400">
            &copy; {{ date('Y') }} {{ config('app.name') }}.
        </footer>
    </div>

    @stack('scripts')
</body>
</html>
