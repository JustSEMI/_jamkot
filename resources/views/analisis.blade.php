<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- PREVENT FOUC & SETUP UI THEME -->
    <script>
        (function() {
            const uiVersion = localStorage.getItem('jamkot-ui-version') || 'v1';
            document.documentElement.setAttribute('data-ui-version', uiVersion);
        })();
    </script>
    <title>Analisis Data | JAMKOT</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,500,0,0&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/panel.css') }}">
    <link rel="stylesheet" href="{{ asset('css/analisis.css') }}">
    <link rel="stylesheet" href="{{ asset('css/mobile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/material3.css') }}">
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
                @if(Route::is('settings.index'))
                <a href="{{ route('panel') }}" class="btn-mobile-settings" title="Back to Panel">
                    <i class="fa-solid fa-house"></i>
                </a>
                @else
                <a href="{{ route('settings.index') }}" class="btn-mobile-settings" title="Settings">
                    <i class="fa-solid fa-gear"></i>
                </a>
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

        <!-- SIDEBAR -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h2>JAMKOT</h2>
            </div>

            <nav class="sidebar-nav">
                @if(auth()->user()->canAccess('admin'))
                <a href="{{ route('admin.users') }}" class="nav-link nav-link-admin {{ Route::is('admin.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users-gear"></i>
                    <span>Admin</span>
                </a>
                @endif
                @if(auth()->user()->canAccess('panel'))
                <a href="{{ route('panel') }}" class="nav-link {{ Route::is('panel') ? 'active' : '' }}">
                    <i class="fa-solid fa-gauge"></i>
                    <span>Panel Utama</span>
                </a>
                @endif
                @if(auth()->user()->canAccess('analisis'))
                <a href="{{ route('analisis') }}" class="nav-link {{ Route::is('analisis') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-simple"></i>
                    <span>Analisis</span>
                </a>
                @endif
                @if(auth()->user()->canAccess('schedule'))
                <a href="{{ route('schedule') }}" class="nav-link {{ Route::is('schedule') ? 'active' : '' }}">
                    <i class="fa-solid fa-clock"></i>
                    <span>Schedules</span>
                </a>
                @endif
                @if(auth()->user()->canAccess('settings'))
                <a href="{{ route('settings.index') }}" class="nav-link {{ Route::is('settings.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-gear"></i>
                    <span>Settings</span>
                </a>
                @endif
                @if(auth()->user()->canAccess('view3d'))
                <a href="{{ route('view3d') }}" class="nav-link {{ Route::is('view3d') ? 'active' : '' }}">
                    <i class="fa-solid fa-cube"></i>
                    <span>3D View</span>
                </a>
                @endif
            </nav>

            <div class="sidebar-footer">
                <span class="user-greeting">Halo, {{ auth()->user()->username ?? 'admin' }}</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-logout-sidebar" title="Logout">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- KONTEN UTAMA -->
        <main class="panel-content">

            <header class="content-header-flex">
                <div>
                    <h1>ANALISIS DATA</h1>
                    <p>Ringkasan akumulasi dan statistik performa sistem JAMKOT.</p>
                </div>
            </header>

            <!-- STATISTIK UTAMA -->
            <div class="summary-grid">
                <div class="glow-card stat-card meter-card meter-card-temperature" style="--meter-angle: {{ min(max(($stats['avg_suhu'] ?? 0) / 40, 0), 1) * 180 }}deg;">
                    <div class="card-title">RATA-RATA SUHU</div>
                    <div class="card-value">{{ number_format($stats['avg_suhu'], 1) }}°C</div>
                    <div class="card-desc">Dari seluruh rekaman data</div>
                </div>

                <div class="glow-card stat-card meter-card meter-card-humidity" style="--meter-angle: {{ min(max(($stats['avg_kelembapan'] ?? 0) / 100, 0), 1) * 180 }}deg;">
                    <div class="card-title">RATA-RATA KELEMBAPAN</div>
                    <div class="card-value">{{ number_format($stats['avg_kelembapan'], 1) }}%</div>
                    <div class="card-desc">Target ideal: 85%</div>
                </div>

                <div class="glow-card stat-card total-log-card">
                    <div class="card-title">TOTAL LOG SISTEM</div>
                    <div class="total-log-content">
                        <span class="total-log-icon material-symbols-rounded">database</span>
                        <div class="card-value">{{ $stats['total_data'] }}</div>
                        <div class="card-desc">Database: MySQL</div>
                    </div>
                </div>
            </div>

            <!-- DETAIL RECORD -->
            <div class="analysis-row">
                <div class="glow-card record-card high">
                    <div class="record-header">
                        <span class="record-icon material-symbols-rounded">arrow_upward</span>
                        <h3 class="section-title" style="margin: 0;">Rekor Tertinggi</h3>
                    </div>
                    <div class="record-grid">
                        <div class="record-item">
                            <span>Suhu</span>
                            <strong>{{ is_null($stats['max_suhu']) ? '--' : $stats['max_suhu'] . '°C' }}</strong>
                        </div>
                        <div class="record-item">
                            <span>Kelembapan</span>
                            <strong>{{ is_null($stats['max_kelembapan']) ? '--' : $stats['max_kelembapan'] . '%' }}</strong>
                        </div>
                    </div>
                </div>

                <div class="glow-card record-card low">
                    <div class="record-header">
                        <span class="record-icon material-symbols-rounded">arrow_downward</span>
                        <h3 class="section-title" style="margin: 0;">Rekor Terendah</h3>
                    </div>
                    <div class="record-grid">
                        <div class="record-item">
                            <span>Suhu</span>
                            <strong>{{ is_null($stats['min_suhu']) ? '--' : $stats['min_suhu'] . '°C' }}</strong>
                        </div>
                        <div class="record-item">
                            <span>Kelembapan</span>
                            <strong>{{ is_null($stats['min_kelembapan']) ? '--' : $stats['min_kelembapan'] . '%' }}</strong>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <script src="{{ asset('js/sidebar.js') }}"></script>
    <!-- BOTTOM NAV FOR MOBILE (M3 Only) -->
    <nav class="bottom-nav">
        @if(auth()->user()->canAccess('panel'))
        <a href="{{ route('panel') }}" class="bottom-nav-link {{ Route::is('panel') ? 'active' : '' }}">
            <div class="bottom-nav-icon-wrapper">
                <i class="fa-solid fa-gauge"></i>
            </div>
            <span>Panel</span>
        </a>
        @endif
        @if(auth()->user()->canAccess('analisis'))
        <a href="{{ route('analisis') }}" class="bottom-nav-link {{ Route::is('analisis') ? 'active' : '' }}">
            <div class="bottom-nav-icon-wrapper">
                <i class="fa-solid fa-chart-simple"></i>
            </div>
            <span>Analisis</span>
        </a>
        @endif
        @if(auth()->user()->canAccess('schedule'))
        <a href="{{ route('schedule') }}" class="bottom-nav-link {{ Route::is('schedule') ? 'active' : '' }}">
            <div class="bottom-nav-icon-wrapper">
                <i class="fa-solid fa-clock"></i>
            </div>
            <span>Schedule</span>
        </a>
        @endif
        @if(auth()->user()->canAccess('admin'))
        <a href="{{ route('admin.users') }}" class="bottom-nav-link {{ Route::is('admin.*') ? 'active' : '' }}">
            <div class="bottom-nav-icon-wrapper">
                <i class="fa-solid fa-users-gear"></i>
            </div>
            <span>Admin</span>
        </a>
        @else
        <a href="{{ route('settings.index') }}" class="bottom-nav-link {{ Route::is('settings.*') ? 'active' : '' }}">
            <div class="bottom-nav-icon-wrapper">
                <i class="fa-solid fa-gear"></i>
            </div>
            <span>Settings</span>
        </a>
        @endif
        @if(auth()->user()->canAccess('view3d'))
        <a href="{{ route('view3d') }}" class="bottom-nav-link {{ Route::is('view3d') ? 'active' : '' }}">
            <div class="bottom-nav-icon-wrapper">
                <i class="fa-solid fa-cube"></i>
            </div>
            <span>3D View</span>
        </a>
        @endif
    </nav>

</body>
</html>
