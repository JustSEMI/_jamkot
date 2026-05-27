@extends('layouts.app')

@section('title', 'SENSOR DHT22')

@section('content')
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
        
        <div id="chart-skeleton" style="position: absolute; top: 60px; left: 1.5rem; right: 1.5rem; bottom: 1.5rem; z-index: 10; overflow: hidden; background: rgba(255,255,255,0.01); border-radius: 8px;">
            <svg width="100%" height="100%" viewBox="0 0 800 250" preserveAspectRatio="none" style="display: block;">
                <defs>
                    <linearGradient id="shimmer-chart" x1="-100%" y1="0%" x2="0%" y2="0%">
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
                <circle cx="630" cy="12" r="4" fill="rgba(16,185,129,0.2)"/>
                <rect x="640" y="8" width="50" height="8" rx="2" fill="rgba(255,255,255,0.03)"/>
                <circle cx="710" cy="12" r="4" fill="rgba(6,182,212,0.2)"/>
                <rect x="720" y="8" width="50" height="8" rx="2" fill="rgba(255,255,255,0.03)"/>

                <!-- Chart Wave Lines -->
                <path d="M 50 160 Q 150 90 250 140 T 450 80 T 650 110 T 780 90" fill="none" stroke="rgba(16,185,129,0.15)" stroke-width="3" stroke-linecap="round"/>
                <path d="M 50 110 Q 150 150 250 90 T 450 130 T 650 70 T 780 120" fill="none" stroke="rgba(6,182,212,0.15)" stroke-width="3" stroke-linecap="round"/>

                <!-- Shimmer Overlay Rect -->
                <rect x="0" y="0" width="800" height="250" fill="url(#shimmer-chart)"/>
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
@endsection

@push('scripts')
    <script src="{{ asset('js/clock.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        window.dataJamkot = @json($riwayatGrafik);
        window.currentSuhu = {{ $latest->suhu ?? 0 }};
        window.currentKelembapan = {{ $latest->kelembapan ?? 0 }};
    </script>
    <script src="{{ asset('js/chart.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/realtime.js') }}?v={{ time() }}"></script>
@endpush
