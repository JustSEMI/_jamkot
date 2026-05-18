<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- UI Theme Setup -->
    <script>
        (function() {
            const uiVersion = localStorage.getItem('jamkot-ui-version') || 'v1';
            document.documentElement.setAttribute('data-ui-version', uiVersion);
        })();
    </script>
    <title>SENSOR DHT22 | JAMKOT</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/panel.css') }}">
    <link rel="stylesheet" href="{{ asset('css/mobile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/material3.css') }}">
    @vite('resources/js/app.js')
</head>

<body>

    <div class="panel-layout">

        <!-- Navigasi -->
        <header class="mobile-top-nav">
            <div class="mobile-logo">JAMKOT</div>
            <button class="btn-toggle-sidebar" id="sidebar-toggle">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="mobile-top-actions">
                @if(auth()->user()->canAccess('admin'))
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

        @include('partials.sidebar')

        <!-- Konten -->
        <main class="panel-content">

            <header class="content-header-flex">
                <div>
                    <h1>SENSOR DHT22</h1>
                    <p>Rincian data suhu dan kelembapan secara real-time.</p>
                </div>

                <!-- Jam -->
                <div class="datetime-widget">
                    <div id="realtime-clock" class="time-display">00:00:00</div>
                    <div id="realtime-date" class="date-display">Memuat...</div>
                </div>
            </header>

            <!-- SECTION 1: KONDISI LINGKUNGAN -->
            <h3 class="section-title" style="margin-top: 1rem; margin-bottom: 1rem; font-size: 1.1rem; color: #9ca3af;">Indikator Suhu & Kelembapan</h3>
            <div class="summary-grid" style="margin-bottom: 2rem;">
                <div class="glow-card sensor-meter-card sensor-meter-temperature" id="card-suhu" style="--meter-angle: {{ min(max(($latest->suhu ?? 0) / 40, 0), 1) * 180 }}deg;">
                    <div class="card-title">SUHU RUANG</div>
                    <div class="status-dot status-suhu {{ $latest ? 'online' : 'offline' }}"></div>
                    <div class="card-value" id="val-suhu">{{ number_format($latest->suhu ?? 0, 1) }}°C</div>
                    <div class="card-desc">Target: 22°C - 28°C</div>
                </div>

                <div class="glow-card sensor-meter-card sensor-meter-humidity" id="card-kelembapan" style="--meter-angle: {{ min(max(($latest->kelembapan ?? 0) / 100, 0), 1) * 180 }}deg;">
                    <div class="card-title">KELEMBAPAN UDARA</div>
                    <div class="status-dot status-kelembapan {{ $latest ? 'online' : 'offline' }}"></div>
                    <div class="card-value" id="val-kelembapan">{{ number_format($latest->kelembapan ?? 0, 1) }}%</div>
                    <div class="card-desc" id="desc-kelembapan" class="{{ ($latest->kelembapan ?? 0) >= $targetKelembapan ? 'text-positive' : '' }}">
                        Target Minimal: <span id="val-target-kelembapan">{{ $targetKelembapan }}</span>%
                    </div>
                </div>
            </div>

            <!-- Grafik -->
            <div class="glow-card chart-wrapper" style="position: relative; min-height: 350px;">
                <h3 class="section-title">Chart Suhu & Kelembapan</h3>
                
                <div id="chart-skeleton" style="position: absolute; top: 60px; left: 1.5rem; right: 1.5rem; bottom: 1.5rem; background: linear-gradient(90deg, rgba(255,255,255,0.03) 25%, rgba(255,255,255,0.08) 50%, rgba(255,255,255,0.03) 75%); background-size: 200% 100%; animation: skeletonLoading 1.5s infinite; border-radius: 8px; z-index: 10;">
                    <style>
                        @keyframes skeletonLoading {
                            0% { background-position: 200% 0; }
                            100% { background-position: -200% 0; }
                        }
                    </style>
                </div>

                <div id="chart-jamkot" style="opacity: 0; transition: opacity 0.5s ease;"></div>
            </div>

            <!-- Log Sensor -->
            <div class="glow-card table-wrapper" id="panel-log-card" style="margin-top: 2rem;">
                <div class="table-header">
                    <h3 class="section-title" style="margin: 0;">Log Sensor Terakhir</h3>
                </div>

                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>WAKTU</th>
                                <th>ID DEVICE</th>
                                <th>STATUS</th>
                                <th class="text-right">NILAI (H | T)</th>
                            </tr>
                        </thead>
                        <tbody id="table-body-log">
                            @forelse($riwayatTabel as $log)
                                <tr>
                                    <td class="text-muted">{{ $log->created_at->diffForHumans() }}</td>
                                    <td>{{ $log->sensor_id }}</td>
                                    <td><span class="badge success">Tercatat</span></td>
                                    <td class="text-right">{{ $log->kelembapan }}% | {{ $log->suhu }}°C</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-muted" style="text-align: center; padding: 2rem;">Belum ada data sensor masuk.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

    <script src="{{ asset('js/clock.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        window.dataJamkot = @json($riwayatGrafik);
        window.currentSuhu = {{ $latest->suhu ?? 0 }};
        window.currentKelembapan = {{ $latest->kelembapan ?? 0 }};
    </script>
    <script src="{{ asset('js/chart.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/sidebar.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/realtime.js') }}?v={{ time() }}"></script>

</body>

</html>
