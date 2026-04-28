@php
    $user = auth()->user();
    $role = $user?->role;

    $nav = [
        'admin' => [
            ['Dashboard', 'admin.dashboard', 'home'],
            ['Siswa', 'admin.students.index', 'users'],
            ['Guru', 'admin.teachers.index', 'briefcase'],
            ['Kelas', 'admin.classes.index', 'collection'],
            ['Jurusan', 'admin.jurusans.index', 'tag'],
            ['Mapel', 'admin.mapels.index', 'book'],
            ['Tahun Ajaran', 'admin.academic-years.index', 'calendar'],
        ],
        'teacher' => [
            ['Dashboard', 'teacher.dashboard', 'home'],
            ['Bank Soal', 'teacher.questions.index', 'document'],
            ['Bab', 'teacher.chapters.index', 'collection'],
            ['Ujian', 'teacher.exams.index', 'pencil'],
            ['Laporan', 'teacher.reports.index', 'chart'],
        ],
        'student' => [
            ['Dashboard', 'student.dashboard', 'home'],
            ['Ujian Aktif', 'student.exams.index', 'pencil'],
        ],
    ][$role] ?? [];

    $icons = [
        'home' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6',
        'users' => 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-5.13a4 4 0 100-8 4 4 0 000 8zm6 0a4 4 0 11-8 0 4 4 0 018 0z',
        'briefcase' => 'M21 13.255A23.93 23.93 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h0',
        'collection' => 'M19 11H5m14-7H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V6a2 2 0 00-2-2z',
        'tag' => 'M7 7h.01M7 3h5a2 2 0 011.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z',
        'book' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
        'calendar' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
        'document' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'pencil' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
        'chart' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
    ];
@endphp

<!-- Mobile overlay -->
<div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
     class="fixed inset-0 z-40 bg-slate-900/50 lg:hidden" x-transition.opacity></div>

<aside x-cloak
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
       class="fixed inset-y-0 left-0 z-50 w-64 transform transition-transform duration-200 ease-in-out
              lg:translate-x-0 lg:fixed
              bg-white border-r border-slate-200
              dark:bg-slate-900 dark:border-slate-800 flex flex-col">

    <div class="h-16 flex items-center gap-3 px-5 border-b border-slate-200 dark:border-slate-800">
        <div class="grid place-items-center h-9 w-9 rounded-lg bg-brand-600 text-white">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
            </svg>
        </div>
        <div class="leading-tight">
            <p class="text-sm font-bold">{{ config('app.name', 'Ujian Sekolah') }}</p>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 capitalize">{{ $role }} panel</p>
        </div>
    </div>

    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
        @foreach ($nav as [$label, $route, $icon])
            @php $active = request()->routeIs($route) || request()->routeIs(str_replace('.index', '.*', $route)); @endphp
            <a href="{{ route($route) }}"
               class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition
                      {{ $active
                          ? 'bg-brand-50 text-brand-700 dark:bg-brand-600/10 dark:text-brand-300'
                          : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white' }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icons[$icon] }}"/>
                </svg>
                {{ $label }}
            </a>
        @endforeach
    </nav>

    <div class="border-t border-slate-200 dark:border-slate-800 p-3">
        <a href="{{ route('profile.edit') }}"
           class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800">
            <div class="h-8 w-8 rounded-full bg-brand-600 text-white grid place-items-center text-xs font-bold">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="truncate">{{ $user->name }}</p>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">
                    {{ $user->email ?? $user->nisn ?? $user->username }}
                </p>
            </div>
        </a>
    </div>
</aside>
