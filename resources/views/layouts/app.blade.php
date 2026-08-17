<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="view-transition" content="same-origin">

    <title>{{ config('app.name', 'SIMPRO') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon_circle.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/htmx.org@1.9.10"></script>
</head>
<body class="bg-background text-on-background font-sans antialiased" hx-boost="true">
    <div class="flex h-screen overflow-hidden bg-surface-container-low" x-data="{ sidebarOpen: false }">
        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen" class="fixed inset-0 z-20 bg-black/50 transition-opacity lg:hidden" @click="sidebarOpen = false"></div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-30 w-64 bg-surface-container-lowest border-r border-surface-border transition duration-300 transform lg:translate-x-0 lg:static lg:inset-0 flex flex-col">
            <!-- Logo -->
            <div class="flex items-center justify-center py-5 border-b border-surface-border bg-surface-container-lowest shrink-0 px-2">
                <img src="{{ asset('images/logo_simpro_lockup_wordmark.png') }}" alt="SIMPRO Logo" class="w-52 h-auto object-contain">
            </div>

            <!-- Navigation -->
            @php $currentPath = request()->path(); @endphp
            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto hide-scrollbar">
                <a href="/dashboard" class="flex items-center px-3 py-2 rounded-lg mb-1 transition-all duration-200 ease-out active:scale-[0.97] hover:translate-x-1 {{ request()->routeIs('dashboard', 'spk.show') ? 'bg-primary-container text-on-primary-container font-medium' : 'text-on-surface hover:bg-surface-container-high' }}">
                    <span class="material-symbols-outlined mr-3 text-[20px]">dashboard</span>
                    <span class="text-body-md">Dashboard</span>
                </a>
                <a href="/spk/create" class="flex items-center px-3 py-2 rounded-lg mb-1 transition-all duration-200 ease-out active:scale-[0.97] hover:translate-x-1 {{ request()->routeIs('spk.create') ? 'bg-primary-container text-on-primary-container font-medium' : 'text-on-surface hover:bg-surface-container-high' }}">
                    <span class="material-symbols-outlined mr-3 text-[20px]">edit_document</span>
                    <span class="text-body-md">Input SPK</span>
                </a>
                <a href="/arsip" class="flex items-center px-3 py-2 rounded-lg mb-1 transition-all duration-200 ease-out active:scale-[0.97] hover:translate-x-1 {{ Str::startsWith($currentPath, 'arsip') ? 'bg-primary-container text-on-primary-container font-medium' : 'text-on-surface hover:bg-surface-container-high' }}">
                    <span class="material-symbols-outlined mr-3 text-[20px]">inventory_2</span>
                    <span class="text-body-md">Arsip</span>
                </a>
                <a href="/pembayaran/cocokkan" class="flex items-center px-3 py-2 rounded-lg mb-1 transition-all duration-200 ease-out active:scale-[0.97] hover:translate-x-1 {{ Str::startsWith($currentPath, 'pembayaran') ? 'bg-primary-container text-on-primary-container font-medium' : 'text-on-surface hover:bg-surface-container-high' }}">
                    <span class="material-symbols-outlined mr-3 text-[20px]">account_balance</span>
                    <span class="text-body-md">Cocokkan Pembayaran</span>
                </a>
                <a href="/laporan" class="flex items-center px-3 py-2 rounded-lg mb-1 transition-all duration-200 ease-out active:scale-[0.97] hover:translate-x-1 {{ Str::startsWith($currentPath, 'laporan') ? 'bg-primary-container text-on-primary-container font-medium' : 'text-on-surface hover:bg-surface-container-high' }}">
                    <span class="material-symbols-outlined mr-3 text-[20px]">assessment</span>
                    <span class="text-body-md">Laporan</span>
                </a>
                <a href="/pengaturan" class="flex items-center px-3 py-2 rounded-lg mb-1 transition-all duration-200 ease-out active:scale-[0.97] hover:translate-x-1 {{ Str::startsWith($currentPath, 'pengaturan') ? 'bg-primary-container text-on-primary-container font-medium' : 'text-on-surface hover:bg-surface-container-high' }}">
                    <span class="material-symbols-outlined mr-3 text-[20px]">settings</span>
                    <span class="text-body-md">Pengaturan</span>
                </a>
            </nav>

            <!-- Logout -->
            <div class="p-4 border-t border-surface-border bg-surface-container-lowest shrink-0">
                <form method="POST" action="{{ route('logout') ?? '#' }}">
                    @csrf
                    <button type="submit" class="flex items-center w-full px-3 py-2 text-error hover:bg-error-container rounded-lg transition-all duration-200 ease-out active:scale-[0.97]">
                        <span class="material-symbols-outlined mr-3 text-[20px]">logout</span>
                        <span class="text-body-md font-medium">Keluar</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Top Navbar -->
            <header class="bg-surface-container-lowest shadow-sm h-16 flex items-center justify-between px-6 z-10 border-b border-surface-border shrink-0">
                <div class="flex items-center">
                    <button @click="sidebarOpen = true" class="text-on-surface focus:outline-none lg:hidden mr-4">
                        <span class="material-symbols-outlined">menu</span>
                    </button>
                    <!-- Search Bar -->
                    <div class="relative hidden sm:block">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 transform -translate-y-1/2 text-outline text-[20px]">search</span>
                        <input type="text" placeholder="Cari..." class="pl-10 pr-4 py-2 border border-outline-variant rounded-lg text-body-sm bg-surface-container focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary w-64 transition-all">
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <button class="text-on-surface-variant hover:text-primary transition-colors relative">
                        <span class="material-symbols-outlined">notifications</span>
                        <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-error ring-2 ring-surface-container-lowest"></span>
                    </button>
                    <div class="flex items-center gap-2 cursor-pointer border-l border-surface-border pl-4">
                        <div class="w-8 h-8 rounded-full bg-primary-container text-primary flex items-center justify-center font-bold text-label-sm">
                            AD
                        </div>
                        <div class="hidden md:block">
                            <p class="text-label-sm font-medium text-on-surface">Admin</p>
                            <p class="text-label-xs text-text-muted">Administrator</p>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto bg-background p-6">
                @yield('content')
            </main>
        </div>
    </div>
    
    <!-- Alpine.js for basic interactivity -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    @stack('scripts')
</body>
</html>
