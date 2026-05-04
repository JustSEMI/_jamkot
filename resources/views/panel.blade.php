<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | JAMKOT</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/panel.css') }}">
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
                    <h1>PANEL UTAMA</h1>
                    <p>Pantau status perangkat dan indikator lingkungan secara real-time.</p>
                </div>

                <!-- WIDGET WAKTU -->
                <div class="datetime-widget">
                    <div id="realtime-clock" class="time-display">00:00:00</div>
                    <div id="realtime-date" class="date-display">Memuat...</div>
                </div>
            </header>

            <!-- KARTU RINGKASAN -->
            <div class="summary-grid">
                <div class="glow-card">
                    <div class="card-title-wrapper">
                        <div class="card-title">INTENSITAS CAHAYA</div>
                        <div class="status-dot {{ $latest ? 'online' : 'offline' }}"></div>
                    </div>
                    <div class="card-value">{{ $latest->cahaya ?? '--' }} Lux</div>
                    <div class="card-desc">Sensor LDR</div>
                </div>

                <div class="glow-card">
                    <div class="card-title-wrapper">
                        <div class="card-title">SUHU RUANG</div>
                        <div class="status-dot {{ $latest ? 'online' : 'offline' }}"></div>
                    </div>
                    <div class="card-value">{{ $latest->suhu ?? '--' }}°C</div>
                    <div class="card-desc">Target: 22°C - 28°C</div>
                </div>

                <div class="glow-card">
                    <div class="card-title-wrapper">
                        <div class="card-title">KELEMBAPAN UDARA</div>
                        <div class="status-dot {{ $latest ? 'online' : 'offline' }}"></div>
                    </div>
                    <div class="card-value">{{ $latest->kelembapan ?? '--' }}%</div>
                    <div class="card-desc {{ ($latest->kelembapan ?? 0) >= $targetKelembapan ? 'text-positive' : '' }}">
                        Target Minimal: {{ $targetKelembapan }}%
                    </div>
                </div>
            </div>

            <!-- GRAFIK -->
            <div class="glow-card chart-wrapper">
                <h3 class="section-title">Tren Suhu & Kelembapan</h3>
                <div id="chart-jamkot"></div>
            </div>

            <!-- TABEL RIWAYAT DATA (Clean Version) -->
            <div class="glow-card table-wrapper">
                <div class="table-header">
                    <h3 class="section-title" style="margin: 0;">Log Sensor Terakhir</h3>
                    <a href="{{ route('panel.export') }}" class="btn-sm"
                        style="text-decoration: none; display: inline-block;">Unduh Laporan</a>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>WAKTU</th>
                            <th>ID DEVICE</th>
                            <th>STATUS</th>
                            <th>POMPA</th>
                            <th class="text-right">NILAI (H | T)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayatTabel as $log)
                            <tr>
                                <td class="text-muted">{{ $log->created_at->diffForHumans() }}</td>
                                <td>{{ $log->sensor_id }}</td>
                                <td><span class="badge success">Tercatat</span></td>
                                <td>
                                    <span class="fw-bold {{ $log->pompa_status == 'ON' ? 'text-blue' : 'text-muted' }}">
                                        {{ $log->pompa_status }}
                                    </span>
                                </td>
                                <td class="text-right">{{ $log->kelembapan }}% | {{ $log->suhu }}°C</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-muted" style="text-align: center; padding: 2rem;">Belum ada data
                                    sensor masuk.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </main>
    </div>

    <script src="{{ asset('js/clock.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        window.dataJamkot = @json($riwayatGrafik);
    </script>
    <script src="{{ asset('js/chart.js') }}"></script>

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