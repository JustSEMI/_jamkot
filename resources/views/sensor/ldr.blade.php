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
    <title>SENSOR CAHAYA | JAMKOT</title>
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
                    <h1>SENSOR CAHAYA (LDR)</h1>
                    <p>Rincian data intensitas cahaya lingkungan secara real-time.</p>
                </div>

                <!-- Jam -->
                <div class="datetime-widget">
                    <div id="realtime-clock" class="time-display">00:00:00</div>
                    <div id="realtime-date" class="date-display">Memuat...</div>
                </div>
            </header>

            <!-- SECTION 1: KONDISI LINGKUNGAN -->
            <h3 class="section-title" style="margin-top: 1rem; margin-bottom: 1rem; font-size: 1.1rem; color: #9ca3af;">Indikator Cahaya</h3>
            <div class="summary-grid" style="margin-bottom: 2rem;">
                <div class="glow-card" id="card-cahaya" style="grid-column: span 3;">
                    <div class="card-title-wrapper">
                        <div class="card-title">INTENSITAS CAHAYA</div>
                        <div class="status-dot status-cahaya {{ $latest ? 'online' : 'offline' }}"></div>
                    </div>
                    <div class="card-value" id="val-cahaya" style="font-size: 3rem;">{{ $latest->cahaya ?? '--' }} Lux</div>
                    <div class="card-desc">Sensor LDR</div>
                </div>
            </div>

            <!-- Grafik -->
            <div class="glow-card chart-wrapper" style="position: relative; min-height: 350px;">
                <h3 class="section-title">Chart Intensitas Cahaya</h3>
                
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
                                <th class="text-right">CAHAYA</th>
                            </tr>
                        </thead>
                        <tbody id="table-body-log">
                            @forelse($riwayatTabel as $log)
                                <tr>
                                    <td class="text-muted">{{ $log->created_at->diffForHumans() }}</td>
                                    <td>{{ $log->sensor_id }}</td>
                                    <td><span class="badge success">Tercatat</span></td>
                                    <td class="text-right">{{ $log->cahaya }} Lux</td>
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
        document.addEventListener('DOMContentLoaded', function() {
            const rawData = @json($riwayatGrafik);
            
            if (!rawData || rawData.length === 0) {
                document.querySelector("#chart-jamkot").innerHTML = 
                    `<div style='text-align: center; color: #6b7280; padding: 2rem 0;'>Belum ada data sensor untuk menampilkan grafik.</div>`;
                return;
            }

            const waktuLabels = rawData.map(item => {
                let date = new Date(item.created_at);
                return date.getHours().toString().padStart(2, '0') + ':' + date.getMinutes().toString().padStart(2, '0');
            });

            const cahayaSeries = rawData.map(item => item.cahaya);
            
            const isM3 = localStorage.getItem('jamkot-ui-version') === 'v1';
            const chartTextColors = isM3 ? '#a2aba7' : '#6b7280';
            const chartGridBorder = isM3 ? '#242c29' : '#1f1f1f';
            
            const options = {
                series: [{
                    name: 'Intensitas Cahaya (Lux)',
                    data: cahayaSeries
                }],
                chart: {
                    height: 300,
                    type: 'area',
                    toolbar: { show: false },
                    background: 'transparent',
                    fontFamily: 'Outfit, Inter, sans-serif'
                },
                colors: ['#fbbf24'], // Warna kuning/amber untuk cahaya
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 3 },
                xaxis: {
                    categories: waktuLabels,
                    labels: { style: { colors: chartTextColors } },
                    axisBorder: { color: chartGridBorder },
                    axisTicks: { color: chartGridBorder }
                },
                yaxis: {
                    labels: { style: { colors: chartTextColors } }
                },
                grid: {
                    borderColor: chartGridBorder,
                    strokeDashArray: 4
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.35,
                        opacityTo: 0.05,
                        stops: [0, 100]
                    }
                },
                legend: {
                    labels: { colors: isM3 ? '#e1e3e1' : '#ededed' }
                }
            };

            const chart = new ApexCharts(document.querySelector("#chart-jamkot"), options);
            chart.render().then(() => {
                const skeleton = document.getElementById('chart-skeleton');
                const chartDiv = document.getElementById('chart-jamkot');
                if (skeleton) skeleton.style.display = 'none';
                if (chartDiv) chartDiv.style.opacity = '1';
            });
        });
    </script>
    <script src="{{ asset('js/sidebar.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/realtime.js') }}?v={{ time() }}"></script>

    @include('partials.bottom-nav')
</body>

</html>
