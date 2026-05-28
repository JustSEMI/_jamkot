<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- PREVENT FOUC & SETUP UI THEME -->
    <script>
        (function() {
            const uiVersion = localStorage.getItem('jamkot-ui-version') || 'v2';
            document.documentElement.setAttribute('data-ui-version', uiVersion);
        })();
    </script>
    <title>Dashboard | JAMKOT</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/pages/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/panel.css') }}">
    <link rel="stylesheet" href="{{ asset('css/mobile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/material3.css') }}">
    @vite('resources/js/app.js')
    <style>
        /* --- GLOBAL THEME ADAPTATION --- */
        html[data-ui-version="v1"] body {
            background: var(--m3-bg, #111413) !important;
            color: var(--m3-on-surface, #e1e3e1) !important;
            font-family: var(--m3-font), sans-serif !important;
        }

        html[data-ui-version="v2"] body {
            background: #0a0a0a !important;
            color: #ededed !important;
            font-family: 'Inter', sans-serif !important;
        }

        /* --- NAVBAR --- */
        .dashboard-navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem 2.5rem;
            transition: all 0.3s ease;
        }

        html[data-ui-version="v1"] .dashboard-navbar {
            background: var(--m3-surface-container-low, #151a18) !important;
            border-bottom: 1px solid var(--m3-outline-variant, #2d3532) !important;
        }

        html[data-ui-version="v2"] .dashboard-navbar {
            background: #050505;
            border-bottom: 1px solid #1a1a1a;
        }

        .dashboard-navbar .brand {
            font-size: 1rem;
            font-weight: 300;
            letter-spacing: 0.2em;
        }

        html[data-ui-version="v1"] .dashboard-navbar .brand {
            color: var(--m3-primary, #80dec5) !important;
            font-weight: 700 !important;
        }

        .dashboard-navbar .nav-right {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        /* --- DASHBOARD LANDING --- */
        .dashboard-landing {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
            text-align: center;
            padding: 2rem;
        }

        .dashboard-landing h1 {
            font-size: 2.5rem;
            font-weight: 300;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }

        html[data-ui-version="v1"] .dashboard-landing h1 {
            color: var(--m3-primary, #80dec5) !important;
            font-weight: 700 !important;
        }

        .dashboard-landing .subtitle {
            font-size: 0.9rem;
            margin-bottom: 3rem;
        }

        html[data-ui-version="v1"] .dashboard-landing .subtitle {
            color: var(--m3-on-surface-variant, #a2aba7) !important;
        }

        html[data-ui-version="v2"] .dashboard-landing .subtitle {
            color: #6b7280;
        }

        /* --- MENU GRID --- */
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1.5rem;
            max-width: 900px;
            width: 100%;
        }

        .menu-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
            padding: 2rem 1.5rem;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* M3 CARD STYLE */
        html[data-ui-version="v1"] .menu-card {
            background: var(--m3-surface-container, #1b221f) !important;
            border-radius: 28px !important;
            border: none !important;
            color: var(--m3-on-surface-variant, #a2aba7) !important;
        }

        html[data-ui-version="v1"] .menu-card:hover {
            background: var(--m3-surface-container-high, #242c29) !important;
            transform: translateY(-4px) scale(1.02) !important;
            color: var(--m3-primary, #80dec5) !important;
        }

        /* NEON GLOW CARD STYLE */
        html[data-ui-version="v2"] .menu-card {
            background: #111111;
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 1.25rem;
            color: #9ca3af;
        }

        html[data-ui-version="v2"] .menu-card:hover {
            border-color: rgba(255, 255, 255, 0.15);
            color: #ededed;
            transform: translateY(-4px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.5);
        }


        .menu-card .menu-icon {
            font-size: 2rem;
            transition: transform 0.3s ease;
        }

        .menu-card:hover .menu-icon {
            transform: scale(1.15);
        }

        .menu-card .menu-label {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-weight: 600;
        }

        /* --- LOGOUT BUTTON --- */
        .btn-logout {
            background: transparent;
            padding: 0.5rem 1.25rem;
            cursor: pointer;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            transition: all 0.2s ease;
        }

        html[data-ui-version="v1"] .btn-logout {
            border: 1px solid var(--m3-outline) !important;
            color: var(--m3-on-surface-variant) !important;
            border-radius: 100px !important;
        }

        html[data-ui-version="v1"] .btn-logout:hover {
            background: var(--m3-surface-container-high) !important;
            border-color: var(--m3-primary) !important;
            color: var(--m3-primary) !important;
        }

        html[data-ui-version="v2"] .btn-logout {
            border: 1px solid #333;
            color: #9ca3af;
            border-radius: 0.5rem;
        }

        html[data-ui-version="v2"] .btn-logout:hover {
            background: #ededed;
            color: #0a0a0a;
            border-color: #ededed;
        }

        .dashboard-footer {
            text-align: center;
            padding: 2.5rem;
            font-size: 0.75rem;
        }

        html[data-ui-version="v1"] .dashboard-footer {
            color: var(--m3-on-surface-variant) !important;
        }

        html[data-ui-version="v2"] .dashboard-footer {
            color: #374151;
        }

        /* --- HAMBURGER & MOBILE NAV --- */
        .hamburger-menu {
            display: none;
            flex-direction: column;
            gap: 5px;
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 5px;
            z-index: 1001;
        }

        .hamburger-menu .bar {
            width: 24px;
            height: 2px;
            background-color: #ededed;
            transition: 0.3s;
            border-radius: 2px;
        }

        html[data-ui-version="v1"] .hamburger-menu .bar {
            background-color: var(--m3-primary) !important;
        }

        .mobile-nav-overlay {
            position: fixed;
            top: 0;
            right: -100%;
            width: 100%;
            height: 100%;
            background: rgba(10, 10, 10, 0.98);
            backdrop-filter: blur(10px);
            z-index: 2000;
            display: flex;
            flex-direction: column;
            padding: 2rem;
            transition: right 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        html[data-ui-version="v1"] .mobile-nav-overlay {
            background: rgba(17, 20, 19, 0.98) !important;
        }

        .mobile-nav-overlay.show {
            right: 0;
        }

        .mobile-nav-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 3rem;
        }

        .close-menu {
            font-size: 2.5rem;
            background: transparent;
            border: none;
            color: #ededed;
            cursor: pointer;
        }

        html[data-ui-version="v1"] .close-menu {
            color: var(--m3-primary) !important;
        }

        .mobile-nav-links {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .m-link {
            text-decoration: none;
            font-size: 1.25rem;
            color: #9ca3af;
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.03);
            transition: all 0.2s ease;
        }

        html[data-ui-version="v1"] .m-link {
            color: var(--m3-on-surface-variant) !important;
            background: var(--m3-surface-container) !important;
            border-radius: 20px !important;
        }

        .m-link i {
            width: 24px;
            color: var(--warna-utama, #10b981);
            text-align: center;
        }

        html[data-ui-version="v1"] .m-link i {
            color: var(--m3-primary) !important;
        }

        .mobile-user {
            font-size: 0.8rem;
            color: #6b7280;
            margin-bottom: 1rem;
            display: block;
        }

        @media (max-width: 768px) {
            .hamburger-menu {
                display: flex;
            }
            .user-name, .logout-form-desktop {
                display: none;
            }
            .dashboard-navbar {
                padding: 1.25rem 1.5rem;
            }
            .dashboard-landing h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>

<body>

    <!-- TOP NAVBAR -->
    <nav class="dashboard-navbar">
        <div class="brand">JAMKOT</div>
        <div class="nav-right">
            <span class="user-name">Halo, <strong>{{ Auth::user()->username }}</strong></span>
            <form action="{{ route('logout') }}" method="POST" class="logout-form-desktop">
                @csrf
                <button type="submit" class="btn-logout">Logout</button>
            </form>
            
            <!-- Hamburger Button for Mobile -->
            <button class="hamburger-menu" id="hamburger-btn">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </button>
        </div>
    </nav>

    <!-- Mobile Navigation Overlay -->
    <div class="mobile-nav-overlay" id="mobile-nav">
        <div class="mobile-nav-header">
            <div class="brand">JAMKOT</div>
            <button class="close-menu" id="close-btn">&times;</button>
        </div>
        <div class="mobile-nav-links">
            <span class="mobile-user">Halo, {{ Auth::user()->username }}</span>
            <a href="{{ route('panel') }}" class="m-link"><i class="fa-solid fa-gauge-high"></i> Panel Utama</a>
            <a href="{{ route('analisis') }}" class="m-link"><i class="fa-solid fa-chart-line"></i> Analisis</a>
            <a href="{{ route('schedule') }}" class="m-link"><i class="fa-solid fa-clock"></i> Schedules</a>
            <a href="{{ route('view3d') }}" class="m-link"><i class="fa-solid fa-cube"></i> 3D View</a>
            <a href="{{ route('settings.index') }}" class="m-link"><i class="fa-solid fa-sliders"></i> Settings</a>
            
            <form action="{{ route('logout') }}" method="POST" style="margin-top: 2rem;">
                @csrf
                <button type="submit" class="btn-logout" style="width: 100%;">Logout</button>
            </form>
        </div>
    </div>

    <!-- LANDING CONTENT -->
    <div class="dashboard-landing">
        <h1>Selamat Datang.</h1>
        <p class="subtitle">JAMKOT &mdash; Jamur Automatic Monitoring &amp; Kontrol Over Telemetry</p>

        <div class="menu-grid">
            @if(Auth::user()->canAccess('panel'))
            <a href="{{ route('panel') }}" class="menu-card" id="menu-panel">
                <i class="fa-solid fa-gauge-high menu-icon"></i>
                <span class="menu-label">Panel Utama</span>
            </a>
            @endif

            @if(Auth::user()->canAccess('analisis'))
            <a href="{{ route('analisis') }}" class="menu-card" id="menu-analisis">
                <i class="fa-solid fa-chart-line menu-icon"></i>
                <span class="menu-label">Analisis</span>
            </a>
            @endif

            @if(Auth::user()->canAccess('schedule'))
            <a href="{{ route('schedule') }}" class="menu-card" id="menu-schedule">
                <i class="fa-solid fa-clock menu-icon"></i>
                <span class="menu-label">Schedules</span>
            </a>
            @endif

            @if(Auth::user()->canAccess('view3d'))
            <a href="{{ route('view3d') }}" class="menu-card" id="menu-view3d">
                <i class="fa-solid fa-cube menu-icon"></i>
                <span class="menu-label">3D View</span>
            </a>
            @endif

            @if(Auth::user()->canAccess('settings'))
            <a href="{{ route('settings.index') }}" class="menu-card" id="menu-settings">
                <i class="fa-solid fa-sliders menu-icon"></i>
                <span class="menu-label">Settings</span>
            </a>
            @endif
        </div>
    </div>

    <footer class="dashboard-footer">
        <p>&copy; 2026 JAMKOT System. Developed by Kelompok 4 TKK (B) 2025</p>
    </footer>

    <script>
        const hbBtn = document.getElementById('hamburger-btn');
        const closeBtn = document.getElementById('close-btn');
        const mobileNav = document.getElementById('mobile-nav');

        hbBtn.addEventListener('click', () => {
            mobileNav.classList.add('show');
        });

        closeBtn.addEventListener('click', () => {
            mobileNav.classList.remove('show');
        });

        document.querySelectorAll('.m-link').forEach(link => {
            link.addEventListener('click', () => {
                mobileNav.classList.remove('show');
            });
        });
    </script>
</body>

</html>