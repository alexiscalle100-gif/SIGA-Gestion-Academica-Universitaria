<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel Académico') — SIGA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen" x-data="{ sidebarOpen: false }">
@php
    $authUser = auth()->user();
    $unreadCount = $authUser ? $authUser->unreadNotificationsCount() : 0;
@endphp

<div class="flex min-h-screen">
    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
         class="fixed inset-0 bg-slate-900/50 z-30 lg:hidden"></div>

    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           class="fixed lg:static inset-y-0 left-0 z-40 w-64 bg-[#0F2744] text-slate-100 flex flex-col transition-transform duration-200 ease-out shrink-0 shadow-xl">
        <div class="px-5 py-5 border-b border-white/10 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-sky-400 to-blue-600 flex items-center justify-center font-bold text-white text-lg shadow-md">
                S
            </div>
            <div>
                <div class="font-bold text-base tracking-tight text-white leading-tight">SIGA Académico</div>
                <div class="text-[11px] text-sky-300/90 font-medium tracking-wide uppercase">Gestión Modular</div>
            </div>
        </div>

        <div class="px-5 py-3.5 bg-white/5 border-b border-white/10">
            <div class="text-[11px] uppercase tracking-wider text-slate-400">Sesión activa</div>
            <div class="text-sm font-semibold text-white truncate mt-0.5">{{ $authUser->name }}</div>
            <div class="mt-1.5 flex items-center gap-2">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold
                    {{ $authUser->isAdmin() ? 'bg-amber-400/20 text-amber-300 border border-amber-400/30' : '' }}
                    {{ $authUser->isTeacher() ? 'bg-emerald-400/20 text-emerald-300 border border-emerald-400/30' : '' }}
                    {{ $authUser->isStudent() ? 'bg-sky-400/20 text-sky-300 border border-sky-400/30' : '' }}">
                    {{ $authUser->roleLabel() }}
                </span>
                @if($authUser->code)
                    <span class="text-[11px] text-slate-400 font-mono">{{ $authUser->code }}</span>
                @endif
            </div>
        </div>

        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto text-sm">
            <div class="px-3 pb-1 text-[11px] font-semibold uppercase tracking-wider text-slate-400">Principal</div>

            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('dashboard') ? 'bg-sky-500/20 text-white font-semibold border border-sky-400/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>Dashboard</span>
            </a>

            @if($authUser->isAdmin())
                <a href="{{ route('admin.offerings.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('admin.offerings.*') ? 'bg-sky-500/20 text-white font-semibold border border-sky-400/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Ofertas Académicas</span>
                </a>
                <a href="{{ route('admin.tuitions.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('admin.tuitions.*') ? 'bg-sky-500/20 text-white font-semibold border border-sky-400/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Matrículas</span>
                </a>
                <div class="px-3 pt-4 pb-1 text-[11px] font-semibold uppercase tracking-wider text-slate-400">Estructura</div>
                <a href="{{ route('admin.faculties.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('admin.faculties.*') ? 'bg-sky-500/20 text-white font-semibold border border-sky-400/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span>Facultades</span>
                </a>
                <a href="{{ route('admin.careers.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('admin.careers.*') ? 'bg-sky-500/20 text-white font-semibold border border-sky-400/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span>Carreras y Pensums</span>
                </a>
                <a href="{{ route('admin.users.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('admin.users.*') ? 'bg-sky-500/20 text-white font-semibold border border-sky-400/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span>Docentes y Estudiantes</span>
                </a>
            @endif

            @if($authUser->isTeacher())
                <a href="{{ route('teacher.offerings.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('teacher.offerings.*') ? 'bg-sky-500/20 text-white font-semibold border border-sky-400/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span>Mis Materias y Tareas</span>
                </a>
            @endif

            @if($authUser->isStudent())
                <a href="{{ route('student.enrollments.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('student.enrollments.*') ? 'bg-sky-500/20 text-white font-semibold border border-sky-400/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Inscripción y Cursos</span>
                </a>
                <a href="{{ route('student.progress') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('student.progress') ? 'bg-sky-500/20 text-white font-semibold border border-sky-400/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span>Avance e Historial</span>
                </a>
            @endif

            <a href="{{ route('reports.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('reports.*') ? 'bg-sky-500/20 text-white font-semibold border border-sky-400/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Reportes</span>
            </a>

            <div class="px-3 pt-4 pb-1 text-[11px] font-semibold uppercase tracking-wider text-slate-400">Cuenta</div>

            <a href="{{ route('notifications.index') }}"
               class="flex items-center justify-between px-3 py-2.5 rounded-lg transition {{ request()->routeIs('notifications.*') ? 'bg-sky-500/20 text-white font-semibold border border-sky-400/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <span class="flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    <span>Notificaciones</span>
                </span>
                @if($unreadCount > 0)
                    <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-rose-500 text-white">{{ $unreadCount }}</span>
                @endif
            </a>

            <a href="{{ route('profile.show') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('profile.*') ? 'bg-sky-500/20 text-white font-semibold border border-sky-400/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span>Mi Perfil</span>
            </a>
        </nav>

        <div class="p-3 border-t border-white/10">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 px-3 py-2.5 rounded-lg text-sm font-medium text-rose-200 bg-rose-500/15 hover:bg-rose-500/25 border border-rose-400/20 transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span>Cerrar Sesión</span>
                </button>
            </form>
        </div>
    </aside>

    <div class="flex-1 flex flex-col min-w-0">
        <header class="bg-white border-b border-slate-200 px-4 sm:px-8 py-4 flex items-center justify-between sticky top-0 z-20">
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h1 class="text-lg sm:text-xl font-bold text-[#0F2744] leading-tight">@yield('header', 'Panel Académico')</h1>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('notifications.index') }}"
                   class="relative p-2 rounded-lg text-slate-500 hover:text-[#0F2744] hover:bg-slate-100 transition"
                   title="Notificaciones">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    @if($unreadCount > 0)
                        <span class="absolute -top-0.5 -right-0.5 w-4 h-4 rounded-full bg-rose-500 text-[10px] font-bold text-white flex items-center justify-center">{{ $unreadCount }}</span>
                    @endif
                </a>

                <a href="{{ route('profile.show') }}"
                   class="hidden sm:flex items-center gap-2.5 pl-3 border-l border-slate-200 hover:opacity-85 transition">
                    <div class="w-9 h-9 rounded-full bg-[#1E3A5F] text-white font-bold text-sm flex items-center justify-center">
                        {{ strtoupper(mb_substr($authUser->name, 0, 1)) }}
                    </div>
                    <div class="text-left leading-tight">
                        <div class="text-sm font-semibold text-slate-800">{{ $authUser->name }}</div>
                        <div class="text-xs text-slate-500">{{ $authUser->email }}</div>
                    </div>
                </a>
            </div>
        </header>

        <main class="flex-1 p-4 sm:p-8 max-w-7xl w-full mx-auto">
            @if(session('success'))
                <div class="mb-6 flex items-center gap-3 px-4 py-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm shadow-sm">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 flex items-center gap-3 px-4 py-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm shadow-sm">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 px-4 py-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm shadow-sm">
                    <div class="font-semibold mb-1">Por favor revisa las siguientes observaciones:</div>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="border-t border-slate-200 bg-white px-8 py-3.5 text-xs text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-2">
            <span>SIGA — Sistema de Gestión Académica Universitaria</span>
            <span>Formato Modular · Gestión 2026</span>
        </footer>
    </div>
</div>
</body>
</html>
