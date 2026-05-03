@extends('layouts.panel')

@section('title', 'Dashboard')

@section('content')
    <header class="content-header">
        <h1>Panel Utama</h1>
        <p>Pantau status perangkat dan indikator lingkungan secara real-time.</p>
    </header>

    <div class="summary-grid">
        <div class="summary-card">
            <div class="card-title-wrapper">
                <div class="card-title">WAKTU SISTEM</div>
                <div class="status-dot online"></div>
            </div>
            <div id="realtime-clock" class="card-value">00:00:00</div>
            <div id="realtime-date" class="card-desc">Memuat...</div>
        </div>

        <div class="summary-card">
            <div class="card-title-wrapper">
                <div class="card-title">SUHU JAMUR</div>
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
    
    <div class="table-container">
        <div class="table-header">
            <h3>Log Sensor Terakhir</h3>
            <button class="btn-sm">Unduh Laporan</button>
        </div>

        <div style="margin-bottom: 2rem; padding: 1rem; background: rgba(255,255,255,0.02); border-radius: 0.5rem; border: 1px solid #1a1a1a;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                <span style="font-size: 0.75rem; color: #9ca3af;">Progress Kelembapan Ideal</span>
                <span style="font-size: 0.75rem; color: #10b981; font-weight: 600;">{{ number_format($persentaseTarget, 1) }}%</span>
            </div>
            <div style="background: #050505; height: 6px; border-radius: 3px; overflow: hidden;">
                <div style="background: #ededed; height: 100%; width: {{ $persentaseTarget }}%; transition: width 1s ease;"></div>
            </div>
        </div>

        @if($riwayat->count() > 0)
            <div class="table-responsive">
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
                        @foreach($riwayat as $log)
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
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <p>Belum ada data sensor masuk.</p>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            
            document.getElementById('realtime-clock').textContent = `${hours}:${minutes}:${seconds}`;
            const dateStr = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;
            document.getElementById('realtime-date').textContent = dateStr;
        }

        setInterval(updateClock, 1000);
        updateClock();
    </script>
@endpush