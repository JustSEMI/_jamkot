<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- PREVENT FOUC & SETUP UI THEME -->
    <script>
        (function() {
            const uiVersion = localStorage.getItem('jamkot-ui-version') || 'v1';
            document.documentElement.setAttribute('data-ui-version', uiVersion);
        })();
    </script>
    
    <title>@yield('title') | JAMKOT</title>
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,500,0,0&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    
    <!-- Global CSS -->
    <link rel="stylesheet" href="{{ asset('css/panel.css') }}">
    <link rel="stylesheet" href="{{ asset('css/mobile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/material3.css') }}">
    
    <!-- Page Specific Styles -->
    @stack('styles')
    
    @vite('resources/js/app.js')
</head>

<body>

    <div class="panel-layout">

        <!-- MOBILE NAV -->
        <header class="mobile-top-nav">
            <div class="mobile-logo">JAMKOT</div>
            <button class="btn-toggle-sidebar" id="sidebar-toggle">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="mobile-top-actions">
                @if(auth()->user()->canAccess('admin'))
                    @if(Route::is('settings.index'))
                    <a href="{{ route('panel') }}" class="btn-mobile-settings" title="Back to Panel">
                        <i class="fa-solid fa-house"></i>
                    </a>
                    @else
                    <a href="{{ route('settings.index') }}" class="btn-mobile-settings" title="Settings">
                        <i class="fa-solid fa-gear"></i>
                    </a>
                    @endif
                @endif
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-mobile-logout" title="Logout">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </button>
                </form>
            </div>
        </header>

        <div class="sidebar-overlay" id="sidebar-overlay"></div>

        @include('partials.sidebar')

        <!-- KONTEN UTAMA -->
        <main class="panel-content">
            <!-- Global Error Alerts -->
            @if(session('error'))
                <div style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); border-radius: 10px; padding: 1rem 1.25rem; margin-bottom: 1.5rem; color: #f87171; font-size: 0.875rem;">
                    <i class="fa-solid fa-circle-exclamation" style="margin-right: 0.5rem;"></i>{{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Modals -->
    @yield('modals')

    <!-- Toast Notification -->
    @if(session('sukses'))
        <div id="toast-modern" class="toast-wrapper">
            <div class="toast-progress"></div>
            <div class="toast-body">
                <div class="toast-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </div>
                <div class="toast-text">
                    <h4>Berhasil</h4>
                    <p>{{ session('sukses') }}</p>
                </div>
                <button class="toast-close" onclick="tutupToastModern()">×</button>
            </div>
        </div>
        <script src="{{ asset('js/toast.js') }}"></script>
    @endif

    <script src="{{ asset('js/sidebar.js') }}"></script>
    
    <!-- Page Specific Scripts -->
    @stack('scripts')
    
    @include('partials.bottom-nav')

</body>

</html>
