<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Ruang Biaya') }}</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="app-shell">
            <!-- Sidebar Navigation -->
            <aside class="sidebar" id="sidebar">
                <div class="sidebar-top">
                    <a class="brand" href="{{ route('dashboard') }}" aria-label="Ruang Biaya">
                        <span class="brand-mark">RB</span><span class="brand-text">Ruang Biaya</span>
                    </a>
                    <button class="sidebar-toggle" type="button" data-sidebar-toggle aria-label="Sembunyikan sidebar">
                        <span class="sidebar-toggle-icon">⟨</span>
                    </button>
                </div>
                <nav class="sidebar-nav" aria-label="Navigasi utama">
                    <a class="sidebar-nav-link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}" href="{{ route('dashboard') }}"><span>◈</span><span class="nav-label">Dashboard</span></a>
                    <a class="sidebar-nav-link {{ request()->routeIs('charges.one-time') ? 'is-active' : '' }}" href="{{ route('charges.one-time') }}"><span>+</span><span class="nav-label">One time Charge</span></a>
                    <a class="sidebar-nav-link {{ request()->routeIs('charges.monthly') ? 'is-active' : '' }}" href="{{ route('charges.monthly') }}"><span>↻</span><span class="nav-label">Manage Service</span></a>
                    @if (auth()->user()?->isManager())
                        <a class="sidebar-nav-link {{ request()->routeIs('admin.account.show') ? 'is-active' : '' }}" href="{{ route('admin.account.show') }}"><span>#</span><span class="nav-label">Manage Akun</span></a>
                    @endif
                </nav>
                <div class="sidebar-intro">
                    <span class="eyebrow">PERSONAL FINANCE</span>
                    <h2>Lebih tenang saat semua tercatat.</h2>
                    <p>Kelola tagihan bulanan dan pengeluaran sekali bayar di satu tempat.</p>
                </div>
                <div class="flex user">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white-700 profile" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3>{{ auth()->user()?->employ_name ?? auth()->user()?->name }}</h3>
                    <form method="POST" action="{{ route('logout') }}" class="flex user">
                        @csrf
                        <button type="submit">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Page Content -->
            <main class="main-content">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>