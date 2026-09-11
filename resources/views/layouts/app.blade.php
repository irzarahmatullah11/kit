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
                    <a class="sidebar-nav-link {{ request()->routeIs('charges.one-time') ? 'is-active' : '' }}" href="{{ route('charges.one-time') }}"><span>+</span><span class="nav-label">One-time / Charge</span></a>
                    <a class="sidebar-nav-link {{ request()->routeIs('charges.monthly') ? 'is-active' : '' }}" href="{{ route('charges.monthly') }}"><span>↻</span><span class="nav-label">Bulanan</span></a>
                    <a class="sidebar-nav-link {{ request()->routeIs('account.show') ? 'is-active' : '' }}" href="{{ route('account.show') }}"><span>#</span><span class="nav-label">Manage Akun</span></a>
                </nav>
                <div class="sidebar-intro">
                    <span class="eyebrow">PERSONAL FINANCE</span>
                    <h2>Lebih tenang saat semua tercatat.</h2>
                    <p>Kelola tagihan bulanan dan pengeluaran sekali bayar di satu tempat.</p>
                </div>
                <div class="sidebar-intro">
                    <h3>{{ auth()->user()?->employ_name ?? auth()->user()?->name }}</h3>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" style="cursor: pointer; padding: 8px 16px; background-color: #ef4444; color: white; border: none; border-radius: 4px;">
                            Log Out
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