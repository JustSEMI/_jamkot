@extends('layouts.app')

@section('title', 'Panel Utama')

@push('styles')
<style>
    /* --- Tabel Responsif --- */
    .table-responsive {
        display: block !important;
        width: 100% !important;
        overflow-x: auto !important;
        overflow-y: hidden !important;
        -webkit-overflow-scrolling: touch !important;
        position: relative !important;
        padding-bottom: 10px !important;
    }

    .data-table {
        width: 100% !important;
        min-width: 800px !important;
        border-collapse: collapse !important;
        table-layout: auto !important;
    }

    .data-table th, .data-table td {
        white-space: nowrap !important;
        padding: 1.25rem 1.5rem !important;
        text-align: left !important;
    }

    @media (max-width: 768px) {
        #panel-log-card {
            padding: 1.5rem 0 !important;
            overflow: hidden !important;
        }
        
        .table-header {
            padding: 0 1.5rem 1rem 1.5rem !important;
        }

        .table-responsive {
            padding: 0 1.5rem !important;
        }

        .table-responsive::-webkit-scrollbar {
            height: 8px !important;
            display: block !important;
        }
        .table-responsive::-webkit-scrollbar-thumb {
            background: var(--warna-utama, #10b981) !important;
            border-radius: 10px !important;
        }
        .table-responsive::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.05) !important;
        }
    }
</style>
@endpush

@section('content')
    <header class="content-header-flex">
        <div>
            <h1>PANEL UTAMA</h1>
            <p>Pantau status perangkat dan indikator lingkungan secara real-time.</p>
        </div>

        <!-- Jam -->
        <div class="datetime-widget">
            <div id="realtime-clock" class="time-display">00:00:00</div>
            <div id="realtime-date" class="date-display">Memuat...</div>
        </div>
    </header>

    <!-- SHORTCUT: Status Perangkat -->
    <a href="{{ route('device') }}" class="device-shortcut-card" id="device-shortcut">
        <div class="device-shortcut-left">
            <span class="device-status-dot offline" id="panel-device-dot"></span>
            <span class="device-shortcut-label">Status Perangkat</span>
            <span class="device-status-badge offline" id="panel-device-badge">Memuat...</span>
        </div>
        <div class="device-shortcut-right">
            <span id="panel-device-detail">—</span>
            <i class="fa-solid fa-chevron-right" style="font-size:0.7rem; color:#4b5563;"></i>
        </div>
    </a>

    <!-- SECTION 1: KONDISI LINGKUNGAN (Law of Proximity) -->
    <h3 class="section-title" style="margin-top: 1rem; margin-bottom: 1rem; font-size: 1.1rem; color: #9ca3af;">Indikator Sensor</h3>
    <div class="summary-grid" style="margin-bottom: 2rem;">
        <div class="glow-card" id="card-cahaya">
            <div class="card-title-wrapper">
                <div class="card-title">INTENSITAS CAHAYA</div>
                <div class="status-dot status-cahaya {{ $latest ? 'online' : 'offline' }}"></div>
            </div>
            <div class="card-value" id="val-cahaya">{{ $latest->cahaya ?? '--' }} Lux</div>
            <div class="card-desc">Sensor LDR</div>
        </div>
        <div class="glow-card sensor-meter-card sensor-meter-temperature" id="card-suhu" style="--meter-angle: {{ min(max(($latest->suhu ?? 0) / 40, 0), 1) * 180 }}deg;">
            <div class="card-title">SUHU RUANG</div>
            <div class="status-dot status-suhu {{ $latest ? 'online' : 'offline' }}"></div>
            <div class="card-value" id="val-suhu">{{ number_format($latest->suhu ?? 0, 1) }}Â°C</div>
            <div class="card-desc">Target: 22Â°C - 28Â°C</div>
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

    <!-- SECTION 2: KONTROL AKTUATOR (Law of Proximity & Doherty Threshold) -->
    <h3 class="section-title" style="margin-bottom: 1rem; font-size: 1.1rem; color: #9ca3af;">Kontrol Pompa</h3>
    <div class="summary-grid" style="margin-bottom: 2.5rem; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
        <div class="glow-card actuator-card" style="display: flex; justify-content: space-between; align-items: center; background: rgba(16, 185, 129, 0.05); border: 1px solid rgba(16, 185, 129, 0.2); padding: 1.5rem;">
            <div>
                <div class="card-title" style="color: var(--warna-utama, #10b981); margin-bottom: 0.25rem;">POMPA AIR (MISTING)</div>
                <div class="card-desc" style="margin: 0;">Kontrol manual kelembapan ruang.</div>
                <div id="pump-status-text" style="font-weight: 600; margin-top: 0.75rem; color: #ededed; display: flex; align-items: center; gap: 0.5rem;">
                    <span class="status-dot offline" id="pump-indicator-dot"></span> <span id="pump-state-label">OFF</span>
                </div>
            </div>
            <div>
                <button id="btn-toggle-pump" style="padding: 1rem 1.5rem; border-radius: 100px; background: var(--warna-utama, #10b981); color: #111; font-weight: bold; border: none; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; transition: all 0.2s; min-height: 48px;" onclick="togglePumpOptimistic()">
                    <i class="fa-solid fa-power-off"></i> <span id="pump-btn-text">NYALAKAN</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Grafik -->
    <div class="glow-card chart-wrapper" style="position: relative; min-height: 350px;">
        <h3 class="section-title">Grafik Suhu & Kelembapan</h3>
        
        <!-- Skeleton Loader UI -->
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
    <div class="glow-card table-wrapper" id="panel-log-card">
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
                        <th>POMPA</th>
                        <th>CAHAYA</th>
                        <th class="text-right">NILAI (H | T)</th>
                    </tr>
                </thead>
                <tbody id="table-body-log">
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
                            <td>{{ $log->cahaya ?? '--' }} Lux</td>
                            <td class="text-right">{{ $log->kelembapan }}% | {{ $log->suhu }}Â°C</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-muted" style="text-align: center; padding: 2rem;">Belum ada data sensor masuk.</td>
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
        window.dataJamkot = @json($riwayatGrafik);
        window.currentSuhu = {{ $latest->suhu ?? 0 }};
        window.currentKelembapan = {{ $latest->kelembapan ?? 0 }};
    </script>
    <script src="{{ asset('js/pages/chart.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/pages/realtime.js') }}?v={{ time() }}"></script>
@endpush
