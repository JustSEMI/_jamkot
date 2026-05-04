<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Penyiraman | JAMKOT</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/panel.css') }}">
    <link rel="stylesheet" href="{{ asset('css/schedule.css') }}">
    <link rel="stylesheet" href="{{ asset('css/mobile.css') }}">
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
        </header>

        <div class="sidebar-overlay" id="sidebar-overlay"></div>

        <!-- SIDEBAR -->
        <aside class="sidebar" id="sidebar">
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
                    <h1>SCHEDULES</h1>
                    <p>Atur jadwal pompa air dan misting untuk menjaga kelembapan kumbung.</p>
                </div>
            </header>

            <form action="{{ route('schedule.store') }}" method="POST">
                @csrf

                <div class="summary-grid">
                    <!-- SESI PAGI -->
                    <div class="schedule-card">
                        <div class="card-header-flex">
                            <h3 class="card-title">Sesi Pagi</h3>
                            <div class="status-dot online"></div>
                        </div>
                        <div class="input-group">
                            <label>JAM MULAI</label>
                            <input type="time" name="jadwal_pagi_mulai" class="input-time-modern"
                                value="{{ $schedule->pagi_mulai ?? '08:00' }}">
                        </div>
                        <div class="input-group mt-1">
                            <label>JAM SELESAI</label>
                            <input type="time" name="jadwal_pagi_selesai" class="input-time-modern"
                                value="{{ $schedule->pagi_selesai ?? '08:05' }}">
                        </div>
                    </div>

                    <!-- SESI SIANG -->
                    <div class="schedule-card">
                        <div class="card-header-flex">
                            <h3 class="card-title">Sesi Siang</h3>
                            <div class="status-dot siang"></div>
                        </div>
                        <div class="input-group">
                            <label>JAM MULAI</label>
                            <input type="time" name="jadwal_siang_mulai" class="input-time-modern"
                                value="{{ $schedule->siang_mulai ?? '12:00' }}">
                        </div>
                        <div class="input-group mt-1">
                            <label>JAM SELESAI</label>
                            <input type="time" name="jadwal_siang_selesai" class="input-time-modern"
                                value="{{ $schedule->siang_selesai ?? '12:05' }}">
                        </div>
                    </div>

                    <!-- SESI SORE -->
                    <div class="schedule-card">
                        <div class="card-header-flex">
                            <h3 class="card-title">Sesi Sore</h3>
                            <div class="status-dot online"></div>
                        </div>
                        <div class="input-group">
                            <label>JAM MULAI</label>
                            <input type="time" name="jadwal_sore_mulai" class="input-time-modern"
                                value="{{ $schedule->sore_mulai ?? '16:00' }}">
                        </div>
                        <div class="input-group mt-1">
                            <label>JAM SELESAI</label>
                            <input type="time" name="jadwal_sore_selesai" class="input-time-modern"
                                value="{{ $schedule->sore_selesai ?? '16:05' }}">
                        </div>
                    </div>
                </div>

                <!-- SMART BACKUP -->
                <div class="schedule-card smart-backup-card">
                    <div class="smart-backup-info">
                        <div class="smart-backup-header">
                            <h3 class="card-title title-blue">Smart Backup</h3>
                            <div class="status-dot backup"></div>
                        </div>
                        <p class="smart-backup-desc">
                            Sistem cerdas: Pompa akan menyala otomatis jika kelembapan ruangan turun di bawah batas yang
                            ditentukan, meskipun di luar jadwal.
                        </p>
                    </div>

                    <div class="smart-backup-control">
                        <label>Batas Kelembapan Minimal:</label>
                        <div class="smart-backup-input-wrapper">
                            <input type="number" name="batas_kelembapan" class="input-time-modern"
                                value="{{ $schedule->batas_kelembapan ?? 80 }}">
                            <span>%</span>
                        </div>
                    </div>
                </div>

                <!-- TOMBOL SIMPAN -->
                <div class="action-row">
                    <button type="submit" class="btn-save">Simpan Konfigurasi</button>
                </div>
            </form>

        </main>
    </div>

    <!-- TOAST NOTIFICATION -->
    @if(session('sukses'))
        <div id="toast-modern" class="toast-wrapper">
            <div class="toast-progress"></div>

            <div class="toast-body">
                <div class="toast-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                        stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </div>

                <div class="toast-text">
                    <h4>Success</h4>
                    <p>{{ session('sukses') }}</p>
                </div>

                <button class="toast-close" onclick="tutupToastModern()">×</button>
            </div>
        </div>
        <script src="{{ asset('js/toast.js') }}"></script>
    @endif

    <script>
        // Sidebar Toggle Logic
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebarOverlay = document.getElementById('sidebar-overlay');

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('show');
            });
        }

        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', () => {
                sidebar.classList.remove('show');
            });
        }
    </script>
</body>

</html>