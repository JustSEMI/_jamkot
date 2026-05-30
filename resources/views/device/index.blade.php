@extends('layouts.app')

@section('title', 'Status Perangkat')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/device.css') }}">
@endpush

@section('content')
    <header class="content-header-flex">
        <div>
            <h1>STATUS PERANGKAT</h1>
            <p>Monitoring kesehatan ESP32 dan konektivitas sensor secara real-time.</p>
        </div>
        <div class="device-live-badge" id="device-live-badge">
            <span class="device-status-dot offline" id="hdr-dot"></span>
            <span id="hdr-status-text">Memuat...</span>
        </div>
    </header>

    {{-- MAIN STATUS CARD --}}
    <div class="device-hero-card" id="device-hero-card">
        <div class="device-hero-left">
            <div class="device-hero-icon" id="device-hero-icon">
                <svg viewBox="0 0 64 64" width="38" height="38" xmlns="http://www.w3.org/2000/svg" style="display: block;">
                    <!-- Antenna trace at top -->
                    <path d="M 16 8 L 48 8 L 48 14 L 44 14 L 44 11 L 20 11 L 20 14 L 16 14 Z" fill="#d69e2e" />
                    <!-- Chip body (Shield) -->
                    <rect x="14" y="16" width="36" height="38" rx="3" fill="#2d3748" stroke="#4a5568" stroke-width="1.5" />
                    <!-- Gold pins on left -->
                    <rect x="9" y="21" width="5" height="2" rx="0.5" fill="#d69e2e" />
                    <rect x="9" y="26" width="5" height="2" rx="0.5" fill="#d69e2e" />
                    <rect x="9" y="31" width="5" height="2" rx="0.5" fill="#d69e2e" />
                    <rect x="9" y="36" width="5" height="2" rx="0.5" fill="#d69e2e" />
                    <rect x="9" y="41" width="5" height="2" rx="0.5" fill="#d69e2e" />
                    <rect x="9" y="46" width="5" height="2" rx="0.5" fill="#d69e2e" />
                    <!-- Gold pins on right -->
                    <rect x="50" y="21" width="5" height="2" rx="0.5" fill="#d69e2e" />
                    <rect x="50" y="26" width="5" height="2" rx="0.5" fill="#d69e2e" />
                    <rect x="50" y="31" width="5" height="2" rx="0.5" fill="#d69e2e" />
                    <rect x="50" y="36" width="5" height="2" rx="0.5" fill="#d69e2e" />
                    <rect x="50" y="41" width="5" height="2" rx="0.5" fill="#d69e2e" />
                    <rect x="50" y="46" width="5" height="2" rx="0.5" fill="#d69e2e" />
                    <!-- Inner shield details -->
                    <rect x="18" y="20" width="28" height="30" rx="1.5" fill="none" stroke="#718096" stroke-width="1" stroke-dasharray="2,2" />
                    <!-- ESP32 Text -->
                    <text x="32" y="36" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-size="8" font-weight="800" text-anchor="middle" fill="#e2e8f0" letter-spacing="0.5">ESP32</text>
                    <!-- Small chip/crystal oscillator on board -->
                    <rect x="26" y="41" width="12" height="6" rx="1" fill="#4a5568" stroke="#718096" stroke-width="0.5" />
                </svg>
            </div>
            <div>
                <div class="device-hero-id" id="hero-device-id">
                    {{ $device?->device_id ?? 'Tidak Ada Data' }}
                </div>
                <div class="device-hero-ip" id="hero-ip">
                    {{ $device?->ip_address ? 'IP: ' . $device->ip_address : 'Belum ada data perangkat' }}
                </div>
            </div>
        </div>
        <div class="device-hero-status">
            <span class="device-big-badge {{ $device && $device->isOnline() ? 'online' : 'offline' }}" id="hero-badge">
                {{ $device && $device->isOnline() ? 'ONLINE' : 'OFFLINE' }}
            </span>
        </div>
    </div>

    {{-- STAT GRID --}}
    <div class="device-stat-grid">

        {{-- UPTIME --}}
        <div class="device-stat-card">
            <div class="device-stat-card-icon uptime">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <div class="device-stat-card-body">
                <div class="device-stat-card-label">Uptime Perangkat</div>
                <div class="device-stat-card-value" id="stat-uptime">
                    {{ $device ? $device->formattedUptime() : '—' }}
                </div>
                <div class="device-stat-card-sub">Sejak terakhir restart</div>
            </div>
        </div>

        {{-- DHT22 --}}
        <div class="device-stat-card">
            <div class="device-stat-card-icon {{ $device?->dht_connected ? 'connected' : 'disconnected' }}" id="icon-dht">
                <i class="fa-solid fa-temperature-half"></i>
            </div>
            <div class="device-stat-card-body">
                <div class="device-stat-card-label">Sensor DHT22</div>
                <div class="device-stat-card-value {{ $device?->dht_connected ? 'text-green' : 'text-red' }}" id="stat-dht">
                    {{ $device ? ($device->dht_connected ? 'Terhubung' : 'Tidak Terhubung') : '—' }}
                </div>
                <div class="device-stat-card-sub">Suhu & Kelembapan</div>
            </div>
        </div>

        {{-- LDR --}}
        <div class="device-stat-card">
            <div class="device-stat-card-icon {{ $device?->ldr_connected ? 'connected' : 'disconnected' }}" id="icon-ldr">
                <i class="fa-solid fa-sun"></i>
            </div>
            <div class="device-stat-card-body">
                <div class="device-stat-card-label">Sensor LDR</div>
                <div class="device-stat-card-value {{ $device?->ldr_connected ? 'text-green' : 'text-red' }}" id="stat-ldr">
                    {{ $device ? ($device->ldr_connected ? 'Terhubung' : 'Tidak Terhubung') : '—' }}
                </div>
                <div class="device-stat-card-sub">Intensitas Cahaya</div>
            </div>
        </div>

        {{-- RSSI --}}
        <div class="device-stat-card">
            <div class="device-stat-card-icon wifi" id="icon-rssi">
                <i class="fa-solid fa-wifi"></i>
            </div>
            <div class="device-stat-card-body">
                <div class="device-stat-card-label">Sinyal WiFi (RSSI)</div>
                <div class="device-stat-card-value" id="stat-rssi">
                    {{ $device?->rssi ? $device->rssi . ' dBm' : '—' }}
                </div>
                <div class="device-stat-card-sub" id="sub-rssi">
                    @if($device?->rssi)
                        @if($device->rssi >= -60) Sinyal Kuat
                        @elseif($device->rssi >= -80) Sinyal Sedang
                        @else Sinyal Lemah @endif
                    @else
                        Tidak diketahui
                    @endif
                </div>
            </div>
        </div>

        {{-- FREE HEAP --}}
        <div class="device-stat-card">
            <div class="device-stat-card-icon memory">
                <i class="fa-solid fa-memory"></i>
            </div>
            <div class="device-stat-card-body">
                <div class="device-stat-card-label">Free Memory (Heap)</div>
                <div class="device-stat-card-value" id="stat-heap">
                    {{ $device?->freeHeapKb() ?? '—' }}
                </div>
                <div class="device-stat-card-sub">RAM bebas ESP32</div>
            </div>
        </div>

        {{-- ESP32 CPU TEMP --}}
        <div class="device-stat-card">
            <div class="device-stat-card-icon temp-chip" id="icon-esp-temp">
                <i class="fa-solid fa-microchip"></i>
            </div>
            <div class="device-stat-card-body">
                <div class="device-stat-card-label">Suhu Internal ESP32</div>
                <div class="device-stat-card-value" id="stat-esp-temp">
                    {{ $device?->esp_temp !== null ? number_format($device->esp_temp, 1) . '°C' : '—' }}
                </div>
                <div class="device-stat-card-sub" id="sub-esp-temp">
                    @if($device?->esp_temp !== null)
                        @if($device->esp_temp >= 85) Overheat (Kritis)
                        @elseif($device->esp_temp >= 70) Panas
                        @else Normal
                        @endif
                    @else
                        Tidak diketahui
                    @endif
                </div>
            </div>
        </div>

        {{-- LAST SEEN --}}
        <div class="device-stat-card">
            <div class="device-stat-card-icon time">
                <i class="fa-solid fa-satellite-dish"></i>
            </div>
            <div class="device-stat-card-body">
                <div class="device-stat-card-label">Heartbeat Terakhir</div>
                <div class="device-stat-card-value" id="stat-last-seen">
                    {{ $device?->last_seen_at?->diffForHumans() ?? '—' }}
                </div>
                <div class="device-stat-card-sub" id="sub-last-seen">
                    {{ $device?->last_seen_at?->format('d M Y, H:i:s') ?? 'Belum pernah terhubung' }}
                </div>
            </div>
        </div>

    </div>

    {{-- INFO FOOTER --}}
    <div class="device-info-footer">
        <i class="fa-solid fa-circle-info"></i>
        Halaman ini diperbarui otomatis setiap <strong>15 detik</strong>.
        Perangkat dianggap <strong>Offline</strong> jika tidak ada heartbeat selama lebih dari <strong>3 menit</strong>.
    </div>
@endsection

@push('scripts')
<script>
    fetchDeviceStatus();
    setInterval(fetchDeviceStatus, 15000);

    function fetchDeviceStatus() {
        fetch('/device/status?t=' + Date.now())
            .then(r => r.json())
            .then(updateUI)
            .catch(() => setOfflineUI());
    }

    function updateUI(d) {
        const online = d.is_online;

        // Header badge
        const hdrDot = document.getElementById('hdr-dot');
        const hdrText = document.getElementById('hdr-status-text');
        if (hdrDot)  hdrDot.className = 'device-status-dot ' + (online ? 'online' : 'offline');
        if (hdrText) hdrText.textContent = d.found ? (online ? 'Online' : 'Offline') : 'Tidak Ada Data';

        if (!d.found) return;

        // Hero card
        const heroBadge = document.getElementById('hero-badge');
        const heroIcon  = document.getElementById('device-hero-icon');
        const heroId    = document.getElementById('hero-device-id');
        const heroIp    = document.getElementById('hero-ip');

        if (heroBadge) {
            heroBadge.className = 'device-big-badge ' + (online ? 'online' : 'offline');
            heroBadge.textContent = online ? 'ONLINE' : 'OFFLINE';
        }
        if (heroIcon) heroIcon.className = 'device-hero-icon ' + (online ? 'online' : 'offline');
        if (heroId)   heroId.textContent = d.device_id ?? '—';
        if (heroIp)   heroIp.textContent = d.ip_address ? 'IP: ' + d.ip_address : '—';

        // Stats
        setText('stat-uptime',    d.uptime_formatted ?? '—');
        setText('stat-last-seen', d.last_seen_at ?? '—');
        setText('sub-last-seen',  d.last_seen_full ?? '—');
        setText('stat-heap',      d.free_heap_kb ?? '—');
        setText('stat-esp-temp',  d.esp_temp_formatted ?? '—');

        // ESP Temp subtext and color
        const tempSub = document.getElementById('sub-esp-temp');
        if (tempSub && d.esp_temp !== null && d.esp_temp !== undefined) {
            tempSub.textContent = d.esp_temp >= 85 ? 'Overheat (Kritis)' : d.esp_temp >= 70 ? 'Panas' : 'Normal';
            const tempEl = document.getElementById('stat-esp-temp');
            if (tempEl) {
                tempEl.style.color = d.esp_temp >= 85 ? '#ef4444' : d.esp_temp >= 70 ? '#fbbf24' : '#34d399';
            }
        }

        // RSSI with colour
        const rssiEl  = document.getElementById('stat-rssi');
        const rssiSub = document.getElementById('sub-rssi');
        if (rssiEl && d.rssi) {
            rssiEl.textContent = d.rssi + ' dBm';
            rssiEl.style.color = d.rssi >= -60 ? '#34d399' : d.rssi >= -80 ? '#fbbf24' : '#f87171';
            if (rssiSub) rssiSub.textContent = d.rssi >= -60 ? 'Sinyal Kuat' : d.rssi >= -80 ? 'Sinyal Sedang' : 'Sinyal Lemah';
        }

        // Sensors
        setSensor('stat-dht', 'icon-dht', d.dht_connected);
        setSensor('stat-ldr', 'icon-ldr', d.ldr_connected);
    }

    function setSensor(valId, iconId, connected) {
        const el   = document.getElementById(valId);
        const icon = document.getElementById(iconId);
        if (el) {
            el.textContent = connected ? 'Terhubung' : 'Tidak Terhubung';
            el.className = 'device-stat-card-value ' + (connected ? 'text-green' : 'text-red');
        }
        if (icon) {
            icon.className = 'device-stat-card-icon ' + (connected ? 'connected' : 'disconnected');
        }
    }

    function setText(id, val) {
        const el = document.getElementById(id);
        if (el) el.textContent = val;
    }

    function setOfflineUI() {
        const hdrDot = document.getElementById('hdr-dot');
        if (hdrDot) hdrDot.className = 'device-status-dot offline';
    }
</script>
@endpush
