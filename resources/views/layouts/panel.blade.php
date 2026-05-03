<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') | JAMKOT</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/panel.css') }}">
    @stack('styles')
    @vite('resources/js/app.js')
</head>
<body>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <header class="mobile-header">
        <h2>JAMKOT</h2>
        <button class="menu-toggle" id="menuToggle">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
        </button>
    </header>

    <div class="panel-layout">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>JAMKOT</h2>
                <button class="close-sidebar" id="closeSidebar">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            
            <nav class="sidebar-nav">
                <a href="{{ route('panel') }}" class="nav-link {{ request()->routeIs('panel') ? 'active' : '' }}">Panel Utama</a>
                <a href="{{ route('jadwal') }}" class="nav-link {{ request()->routeIs('jadwal') ? 'active' : '' }}">Schedules</a>
                <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.index') ? 'active' : '' }}">Settings</a>    
            </nav>

            <div class="sidebar-footer">
                <span class="user-greeting">Halo, {{ auth()->user()->username ?? 'admin' }}</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-logout-sidebar">Logout</button>
                </form>
            </div>
        </aside>

        <main class="panel-content">
            @yield('content')
        </main>
    </div>

    <script>
        // Sidebar Toggle Logic
        const menuToggle = document.getElementById('menuToggle');
        const closeSidebar = document.getElementById('closeSidebar');
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        function toggleSidebar() {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
            document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
        }

        if (menuToggle) menuToggle.addEventListener('click', toggleSidebar);
        if (closeSidebar) closeSidebar.addEventListener('click', toggleSidebar);
        if (overlay) overlay.addEventListener('click', toggleSidebar);
    </script>
    @stack('scripts')
</body>
</html>
