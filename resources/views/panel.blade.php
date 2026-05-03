<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | JAMKOT</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/panel.css') }}">
    @vite('resources/js/app.js')
</head>

<body>

    <div class="panel-layout">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>JAMKOT</h2>
            </div>

            <nav class="sidebar-nav">
                <a href="{{ route('panel') }}" class="nav-link active">Panel Utama</a>
                <a href="{{ route('schedule') }}" class="nav-link">Schedules</a>
                <a href="{{ route('settings.index') }}" class="nav-link">Settings</a>
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
            <!-- HEADER BARU -->
            <header class="content-header"
                style="display: flex; justify-content: space-between; align-items: flex-end;">
                <div>
                    <h1>PANEL UTAMA</h1>
                    <p>Pantau status perangkat dan indikator lingkungan secara real-time.</p>
                </div>
                <div
                    style="text-align: right; background: #111111; padding: 0.75rem 1.5rem; border-radius: 0.5rem; border: 1px solid #1f1f1f;">
                    <div id="realtime-clock"
                        style="font-size: 1.25rem; font-weight: 500; color: #ededed; margin-bottom: 2px;">00:00:00</div>
                    <div id="realtime-date" style="font-size: 0.75rem; color: #9ca3af;">Memuat...</div>
                </div>
            </header>

            <div class="summary-grid">

                <div class="summary-card">
                    <div class="card-title-wrapper">
                        <div class="card-title">INTENSITAS CAHAYA</div>
                        <div class="status-dot {{ $latest ? 'online' : 'offline' }}"></div>
                    </div>
                    <div class="card-value">{{ $latest->cahaya ?? '--' }} Lux</div>
                    <div class="card-desc">Sensor LDR</div>
                </div>

                <div class="summary-card">
                    <div class="card-title-wrapper">
                        <div class="card-title">SUHU RUANG</div>
                        <div class="status-dot {{ $latest ? 'online' : 'offline' }}"></div>
                    </div>
                    <div class="card-value">{{ $latest->suhu ?? '--' }}°C</div>
                    <div class="card-desc">Target: 22°C - 28°C</div>
                </div>

                <div class="summary-card">
                    <div class="card-title-wrapper">
                        <div class="card-title">KELEMBAPAN UDARA</div>
                        <div class="status-dot {{ $latest ? 'online' : 'offline' }}"></div>
                    </div>
                    <div class="card-value">{{ $latest->kelembapan ?? '--' }}%</div>
                    <div class="card-desc {{ ($latest->kelembapan ?? 0) >= $targetKelembapan ? 'positive' : '' }}">
                        Target Minimal: {{ $targetKelembapan }}%
                    </div>
                </div>

            </div>

            <div
                style="background-color: #111111; border: 1px solid #1f1f1f; border-radius: 0.75rem; padding: 1.5rem; margin-top: 2rem;">
                <h3
                    style="font-size: 1rem; color: #9ca3af; font-weight: 500; margin-bottom: 1rem; text-transform: uppercase; letter-spacing: 0.05em;">
                    Tren Suhu & Kelembapan</h3>

                <div id="chart-jamkot"></div>
            </div>

            <div class="table-container">
                <div class="table-header">
                    <h3>Log Sensor Terakhir</h3>
                    <button class="btn-sm">Unduh Laporan</button>
                </div>

                <div
                    style="margin-bottom: 2rem; padding: 1rem; background: rgba(255,255,255,0.02); border-radius: 0.5rem; border: 1px solid #1a1a1a;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span style="font-size: 0.75rem; color: #9ca3af;">Progress Kelembapan Ideal</span>
                        <span style="font-size: 0.75rem; color: #10b981; font-weight: 600;">{{ number_format($persentaseTarget, 1) }}%</span>
                    </div>
                    <div style="background: #050505; height: 6px; border-radius: 3px; overflow: hidden;">
                        <div
                            style="background: #ededed; height: 100%; width: {{ $persentaseTarget }}%; transition: width 1s ease;">
                        </div>
                    </div>
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
                        @forelse($riwayat as $log)
                            <tr>
                                <td style="color: #9ca3af;">{{ $log->created_at->diffForHumans() }}</td>
                                <td>{{ $log->sensor_id }}</td>
                                <td><span class="badge success">Tercatat</span></td>
                                <td>
                                    <span style="color: {{ $log->pompa_status == 'ON' ? '#3b82f6' : '#6b7280' }}">
                                        {{ $log->pompa_status }}
                                    </span>
                                </td>
                                <td class="text-right">{{ $log->kelembapan }}% | {{ $log->suhu }}°C</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: #6b7280; padding: 2rem;">Belum ada data
                                    sensor masuk.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </main>
    </div>

    <script src="{{ asset('js/clock.js') }}"></script>
    <!-- Load Library ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        window.dataJamkot = @json($riwayat);
    </script>
    <script src="{{ asset('js/chart.js') }}"></script>

</body>

</html>