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
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/panel.css') }}">
    <link rel="stylesheet" href="{{ asset('css/mobile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/material3.css') }}">
    @vite('resources/js/app.js')
    <style>
        /* Dashboard landing override */
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

        .dashboard-landing .subtitle {
            color: #6b7280;
            font-size: 0.9rem;
            margin-bottom: 3rem;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1.25rem;
            max-width: 750px;
            width: 100%;
        }

        .menu-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.75rem;
            padding: 1.75rem 1rem;
            background: #141414;
            border: 1px solid #262626;
            border-radius: 1rem;
            text-decoration: none;
            color: #9ca3af;
            transition: all 0.3s ease;
        }

        .menu-card:hover {
            border-color: var(--warna-utama, #10b981);
            color: #ededed;
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.4);
        }

        .menu-card .menu-icon {
            font-size: 1.75rem;
        }

        .menu-card .menu-label {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 500;
        }

        .dashboard-navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem 2.5rem;
            border-bottom: 1px solid #1a1a1a;
            background: #050505;
        }

        .dashboard-navbar .brand {
            font-size: 1rem;
            font-weight: 300;
            letter-spacing: 0.2em;
            color: #ededed;
        }

        .dashboard-navbar .nav-right {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .dashboard-navbar .user-name {
            font-size: 0.8rem;
            color: #9ca3af;
        }

        .dashboard-footer {
            text-align: center;
            padding: 2rem;
            color: #374151;
            font-size: 0.75rem;
        }
    </style>
</head>

<body style="display: flex; flex-direction: column; min-height: 100vh; background: #0a0a0a; color: #ededed; font-family: 'Inter', sans-serif;">

    <!-- TOP NAVBAR -->
    <nav class="dashboard-navbar">
        <span class="brand">JAMKOT</span>
        <div class="nav-right">
            <span class="user-name">Halo, <strong>{{ Auth::user()->username }}</strong></span>
            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn-logout" style="background: transparent; border: 1px solid #333; color: #9ca3af; padding: 0.4rem 0.9rem; border-radius: 0.375rem; cursor: pointer; font-size: 0.75rem; transition: all 0.2s;">
                    Logout
                </button>
            </form>
        </div>
    </nav>

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

</body>

</html>