<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analisis Data | JAMKOT</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/panel.css') }}">
    <link rel="stylesheet" href="{{ asset('css/analisis.css') }}">
    @vite('resources/js/app.js')
</head>

<body>

    <div class="panel-layout">
        
        <!-- SIDEBAR -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>JAMKOT</h2>
            </div>

            <nav class="sidebar-nav">
                <a href="{{ route('panel') }}" class="nav-link {{ Route::is('panel') ? 'active' : '' }}">
                    <i class="fa-solid fa-gauge-high"></i> Panel Utama
                </a>
                <a href="{{ route('analisis') }}" class="nav-link {{ Route::is('analisis') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-simple"></i> Analisis
                </a>
                <a href="{{ route('schedule') }}" class="nav-link {{ Route::is('schedule') ? 'active' : '' }}">
                    <i class="fa-solid fa-calendar-days"></i> Schedules
                </a>
                <a href="{{ route('settings.index') }}" class="nav-link {{ Route::is('settings.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-gear"></i> Settings
                </a>
            </nav>

            <div class="sidebar-footer">
                <span class="user-greeting">Halo, {{ auth()->user()->username ?? 'admin' }}</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-logout-sidebar">
                        <i class="fa-solid fa-right-from-bracket"></i> Logout
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
                <div class="glow-card stat-card">
                    <div class="card-title">RATA-RATA SUHU</div>
                    <div class="card-value">{{ number_format($stats['avg_suhu'], 1) }}°C</div>
                    <div class="card-desc">Dari {{ $stats['total_data'] }} rekaman data</div>
                </div>

                <div class="glow-card stat-card">
                    <div class="card-title">RATA-RATA KELEMBAPAN</div>
                    <div class="card-value">{{ number_format($stats['avg_kelembapan'], 1) }}%</div>
                    <div class="card-desc">Target ideal: 85%</div>
                </div>

                <div class="glow-card stat-card">
                    <div class="card-title">TOTAL LOG SISTEM</div>
                    <div class="card-value">{{ $stats['total_data'] }}</div>
                    <div class="card-desc">Database: MySQL</div>
                </div>
            </div>

            <!-- DETAIL RECORD -->
            <div class="analysis-row">
                <div class="glow-card record-card high">
                    <div class="record-header">
                        <span class="record-icon">🔥</span>
                        <h3 class="section-title" style="margin: 0;">Rekor Tertinggi</h3>
                    </div>
                    <div class="record-grid">
                        <div class="record-item">
                            <span>Suhu</span>
                            <strong>{{ $stats['max_suhu'] }}°C</strong>
                        </div>
                        <div class="record-item">
                            <span>Kelembapan</span>
                            <strong>{{ $stats['max_kelembapan'] }}%</strong>
                        </div>
                    </div>
                </div>

                <div class="glow-card record-card low">
                    <div class="record-header">
                        <span class="record-icon">❄️</span>
                        <h3 class="section-title" style="margin: 0;">Rekor Terendah</h3>
                    </div>
                    <div class="record-grid">
                        <div class="record-item">
                            <span>Suhu</span>
                            <strong>{{ $stats['min_suhu'] }}°C</strong>
                        </div>
                        <div class="record-item">
                            <span>Kelembapan</span>
                            <strong>{{ $stats['min_kelembapan'] }}%</strong>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>

</body>
</html>