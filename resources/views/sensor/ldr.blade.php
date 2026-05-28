@extends('layouts.app')

@section('title', 'Sensor Cahaya')

@section('content')
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
        
        <div id="chart-skeleton" style="position: absolute; top: 60px; left: 1.5rem; right: 1.5rem; bottom: 1.5rem; z-index: 10; overflow: hidden; background: rgba(255,255,255,0.01); border-radius: 8px;">
            <svg width="100%" height="100%" viewBox="0 0 800 250" preserveAspectRatio="none" style="display: block;">
                <defs>
                    <linearGradient id="shimmer-chart-ldr" x1="-100%" y1="0%" x2="0%" y2="0%">
                        <stop offset="0%" stop-color="rgba(255,255,255,0)" />
                        <stop offset="50%" stop-color="rgba(255,255,255,0.06)" />
                        <stop offset="100%" stop-color="rgba(255,255,255,0)" />
                        <animate attributeName="x1" from="-100%" to="100%" dur="1.5s" repeatCount="indefinite" />
                        <animate attributeName="x2" from="0%" to="200%" dur="1.5s" repeatCount="indefinite" />
                    </linearGradient>
                </defs>

                <!-- Grid Lines -->
                <line x1="50" y1="30" x2="780" y2="30" stroke="rgba(255,255,255,0.05)" stroke-dasharray="4"/>
                <line x1="50" y1="80" x2="780" y2="80" stroke="rgba(255,255,255,0.05)" stroke-dasharray="4"/>
                <line x1="50" y1="130" x2="780" y2="130" stroke="rgba(255,255,255,0.05)" stroke-dasharray="4"/>
                <line x1="50" y1="180" x2="780" y2="180" stroke="rgba(255,255,255,0.05)" stroke-dasharray="4"/>
                <line x1="50" y1="220" x2="780" y2="220" stroke="rgba(255,255,255,0.08)"/>

                <!-- Y-Axis Label Placeholders -->
                <rect x="15" y="25" width="22" height="8" rx="2" fill="rgba(255,255,255,0.03)"/>
                <rect x="15" y="75" width="22" height="8" rx="2" fill="rgba(255,255,255,0.03)"/>
                <rect x="15" y="125" width="22" height="8" rx="2" fill="rgba(255,255,255,0.03)"/>
                <rect x="15" y="175" width="22" height="8" rx="2" fill="rgba(255,255,255,0.03)"/>

                <!-- X-Axis Label Placeholders -->
                <rect x="90" y="230" width="35" height="8" rx="2" fill="rgba(255,255,255,0.03)"/>
                <rect x="230" y="230" width="35" height="8" rx="2" fill="rgba(255,255,255,0.03)"/>
                <rect x="370" y="230" width="35" height="8" rx="2" fill="rgba(255,255,255,0.03)"/>
                <rect x="510" y="230" width="35" height="8" rx="2" fill="rgba(255,255,255,0.03)"/>
                <rect x="650" y="230" width="35" height="8" rx="2" fill="rgba(255,255,255,0.03)"/>

                <!-- Legend Placeholders -->
                <circle cx="700" cy="12" r="4" fill="rgba(251,191,36,0.2)"/>
                <rect x="710" y="8" width="60" height="8" rx="2" fill="rgba(255,255,255,0.03)"/>

                <!-- Chart Wave Line -->
                <path d="M 50 140 Q 150 70 250 160 T 450 100 T 650 130 T 780 80" fill="none" stroke="rgba(251,191,36,0.15)" stroke-width="3" stroke-linecap="round"/>

                <!-- Shimmer Overlay Rect -->
                <rect x="0" y="0" width="800" height="250" fill="url(#shimmer-chart-ldr)"/>
            </svg>
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
@endsection

@push('scripts')
    <script src="{{ asset('js/utils/clock.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rawData = @json($riwayatGrafik);
            
            const isM3 = localStorage.getItem('jamkot-ui-version') === 'v1';
            const chartTextColors = isM3 ? '#a2aba7' : '#6b7280';
            const chartGridBorder = isM3 ? '#242c29' : '#1f1f1f';

            if (!rawData || rawData.length === 0) {
                document.querySelector("#chart-jamkot").innerHTML = 
                    `<div style='display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 250px; color: ${chartTextColors}; font-size: 0.875rem;'>` +
                    `<i class="fa-solid fa-chart-line" style="font-size: 2rem; margin-bottom: 1rem; opacity: 0.5;"></i>` +
                    `<div>Belum ada data sensor untuk menampilkan grafik.</div>` +
                    `</div>`;
                const skeleton = document.getElementById('chart-skeleton');
                const chartDiv = document.getElementById('chart-jamkot');
                if (skeleton) skeleton.style.display = 'none';
                if (chartDiv) chartDiv.style.opacity = '1';
                return;
            }

            const waktuLabels = rawData.map(item => {
                let date = new Date(item.created_at);
                return date.getHours().toString().padStart(2, '0') + ':' + date.getMinutes().toString().padStart(2, '0');
            });

            const cahayaSeries = rawData.map(item => item.cahaya);
            
            const options = {
                series: [{
                    name: 'Intensitas Cahaya (Lux)',
                    data: cahayaSeries
                }],
                chart: {
                    height: 300,
                    type: 'line',
                    toolbar: { show: false },
                    background: 'transparent',
                    fontFamily: 'Outfit, Inter, sans-serif'
                },
                colors: ['#fbbf24'],
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 3 },
                markers: {
                    size: 4,
                    strokeWidth: 2,
                    hover: {
                        size: 6
                    }
                },
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
    <script src="{{ asset('js/pages/realtime.js') }}?v={{ time() }}"></script>
@endpush
