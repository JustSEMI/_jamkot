@extends('layouts.panel')

@section('title', 'Jadwal Penyiraman')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/jadwal.css') }}">
@endpush

@section('content')
    <div class="content-header">
        <h1>Schedules</h1>
        <p>Atur jadwal pompa air dan misting untuk menjaga kelembapan kumbung.</p>
    </div>

    <form action="#" method="POST">
        @csrf

        <div class="summary-grid">
            <div class="summary-card">
                <div class="card-title-wrapper">
                    <h3 class="card-title">Sesi Pagi</h3>
                    <div class="status-dot online"></div>
                </div>
                <div class="input-group">
                    <label>Jam Mulai</label>
                    <input type="time" name="jadwal_pagi_mulai" value="{{ date('H:i', strtotime($schedule->pagi_mulai ?? '08:00')) }}">
                </div>
                <div class="input-group">
                    <label>Jam Selesai</label>
                    <input type="time" name="jadwal_pagi_selesai" value="{{ date('H:i', strtotime($schedule->pagi_selesai ?? '08:05')) }}">
                </div>
            </div>

            <div class="summary-card">
                <div class="card-title-wrapper">
                    <h3 class="card-title">Sesi Siang</h3>
                    <div class="status-dot siang"></div>
                </div>
                <div class="input-group">
                    <label>Jam Mulai</label>
                    <input type="time" name="jadwal_siang_mulai" value="{{ date('H:i', strtotime($schedule->siang_mulai ?? '12:00')) }}">
                </div>
                <div class="input-group">
                    <label>Jam Selesai</label>
                    <input type="time" name="jadwal_siang_selesai" value="{{ date('H:i', strtotime($schedule->siang_selesai ?? '12:05')) }}">
                </div>
            </div>

            <div class="summary-card">
                <div class="card-title-wrapper">
                    <h3 class="card-title">Sesi Sore</h3>
                    <div class="status-dot online"></div>
                </div>
                <div class="input-group">
                    <label>Jam Mulai</label>
                    <input type="time" name="jadwal_sore_mulai" value="{{ date('H:i', strtotime($schedule->sore_mulai ?? '16:00')) }}">
                </div>
                <div class="input-group">
                    <label>Jam Selesai</label>
                    <input type="time" name="jadwal_sore_selesai" value="{{ date('H:i', strtotime($schedule->sore_selesai ?? '16:05')) }}">
                </div>
            </div>
        </div>

        <div class="table-container">
            <div>
                <div class="card-title-wrapper">
                    <h3 class="card-title" style="color: #3b82f6;">Smart Backup</h3>
                    <div class="status-dot backup"></div>
                </div>
                <p style="font-size: 0.75rem; color: #6b7280; margin-top: 1rem; line-height: 1.5;">
                    Pompa menyala di luar jadwal jika kelembapan turun.
                </p>
                <div class="input-group">
                    <label>Batas Kelembapan</label>
                    <div class="input-with-suffix">
                        <input type="number" name="batas_kelembapan" value="{{ $schedule->batas_kelembapan ?? 80 }}">
                        <span>%</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="action-row">
            <button type="submit" class="btn-save">Simpan Konfigurasi</button>
        </div>
    </form>
@endsection

@if(session('sukses'))
    @push('scripts')
        <div id="toast-sukses" class="toast-notification">
            <span class="toast-text">{{ session('sukses') }}</span>
        </div>

        <script>
            setTimeout(() => {
                const toast = document.getElementById('toast-sukses');
                if(toast) {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateY(20px)';
                    setTimeout(() => toast.remove(), 300);
                }
            }, 3000);
        </script>
    @endpush
@endif