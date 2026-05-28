@extends('layouts.app')

@section('title', 'Schedules')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/schedule.css') }}">
<style>
    /* --- MATERIAL 3 EXPRESSIVE OVERRIDES FOR SCHEDULE PAGE --- */
    html[data-ui-version="v1"] .schedule-card {
        background: var(--m3-surface-container) !important;
        border: none !important;
        border-radius: 28px !important;
        padding: 2rem !important;
        box-shadow: none !important;
    }

    html[data-ui-version="v1"] .schedule-card:hover {
        background: var(--m3-surface-container-high) !important;
        transform: translateY(-2px) !important;
    }

    html[data-ui-version="v1"] .card-header-flex {
        border-bottom-color: var(--m3-outline-variant) !important;
    }

    html[data-ui-version="v1"] .schedule-card .card-title {
        color: var(--m3-primary) !important;
        font-family: var(--m3-font) !important;
        font-weight: 700 !important;
    }

    html[data-ui-version="v1"] .input-group label {
        color: var(--m3-on-surface-variant) !important;
        font-family: var(--m3-font) !important;
        font-weight: 600 !important;
    }

    html[data-ui-version="v1"] .input-time-modern {
        background-color: var(--m3-surface-container-highest) !important;
        color: var(--m3-on-surface) !important;
        border: 1px solid var(--m3-outline-variant) !important;
        border-radius: 16px !important;
        font-family: var(--m3-font) !important;
    }

    html[data-ui-version="v1"] .input-time-modern:focus {
        border-color: var(--m3-primary) !important;
        box-shadow: 0 0 0 3px rgba(128, 222, 197, 0.15) !important;
    }

    html[data-ui-version="v1"] .input-time-modern::-webkit-calendar-picker-indicator {
        filter: invert(1) brightness(0.9);
        cursor: pointer;
    }

    html[data-ui-version="v1"] .smart-backup-header .title-blue {
        color: var(--m3-primary) !important;
    }

    html[data-ui-version="v1"] .smart-backup-desc {
        color: var(--m3-on-surface-variant) !important;
        font-family: var(--m3-font) !important;
    }

    html[data-ui-version="v1"] .smart-backup-control {
        background: var(--m3-surface-container-low) !important;
        border: 1px solid var(--m3-outline-variant) !important;
        border-radius: 20px !important;
        padding: 1.25rem 2rem !important;
    }

    html[data-ui-version="v1"] .smart-backup-input-wrapper {
        background: var(--m3-surface-container-highest) !important;
        border-color: var(--m3-outline-variant) !important;
        border-radius: 12px !important;
    }

    html[data-ui-version="v1"] .smart-backup-input-wrapper span {
        color: var(--m3-on-surface-variant) !important;
        font-weight: 600 !important;
    }

    html[data-ui-version="v1"] .status-dot.pagi {
        background-color: #a8c7ff !important;
        box-shadow: 0 0 10px rgba(168, 199, 255, 0.4) !important;
    }
    
    html[data-ui-version="v1"] .status-dot.siang {
        background-color: #f9d949 !important;
        box-shadow: 0 0 10px rgba(249, 217, 73, 0.4) !important;
    }

    html[data-ui-version="v1"] .status-dot.sore {
        background-color: var(--m3-tertiary) !important;
        box-shadow: 0 0 10px rgba(255, 182, 143, 0.4) !important;
    }

    html[data-ui-version="v1"] .status-dot.backup {
        background-color: var(--m3-primary) !important;
        box-shadow: 0 0 10px rgba(128, 222, 197, 0.4) !important;
    }
</style>
@endpush

@section('content')
    <header class="content-header-flex">
        <div>
            <h1>SCHEDULES</h1>
            <p>Atur jadwal pompa air dan misting untuk menjaga kelembapan kumbung.</p>
        </div>
    </header>

    <form action="{{ route('schedule.store') }}" method="POST">
        @csrf

        <div class="summary-grid">
            <!-- SESI PAGI -->
            <div class="schedule-card">
                <div class="card-header-flex">
                    <h3 class="card-title">Sesi Pagi</h3>
                    <div class="status-dot pagi"></div>
                </div>
                <div class="input-group">
                    <label>JAM MULAI</label>
                    <input type="time" name="jadwal_pagi_mulai" class="input-time-modern"
                        value="{{ $schedule->pagi_mulai ?? '08:00' }}">
                </div>
                <div class="input-group mt-1">
                    <label>JAM SELESAI</label>
                    <input type="time" name="jadwal_pagi_selesai" class="input-time-modern"
                        value="{{ $schedule->pagi_selesai ?? '08:05' }}">
                </div>
            </div>

            <!-- SESI SIANG -->
            <div class="schedule-card">
                <div class="card-header-flex">
                    <h3 class="card-title">Sesi Siang</h3>
                    <div class="status-dot siang"></div>
                </div>
                <div class="input-group">
                    <label>JAM MULAI</label>
                    <input type="time" name="jadwal_siang_mulai" class="input-time-modern"
                        value="{{ $schedule->siang_mulai ?? '12:00' }}">
                </div>
                <div class="input-group mt-1">
                    <label>JAM SELESAI</label>
                    <input type="time" name="jadwal_siang_selesai" class="input-time-modern"
                        value="{{ $schedule->siang_selesai ?? '12:05' }}">
                </div>
            </div>

            <!-- SESI SORE -->
            <div class="schedule-card">
                <div class="card-header-flex">
                    <h3 class="card-title">Sesi Sore</h3>
                    <div class="status-dot sore"></div>
                </div>
                <div class="input-group">
                    <label>JAM MULAI</label>
                    <input type="time" name="jadwal_sore_mulai" class="input-time-modern"
                        value="{{ $schedule->sore_mulai ?? '16:00' }}">
                </div>
                <div class="input-group mt-1">
                    <label>JAM SELESAI</label>
                    <input type="time" name="jadwal_sore_selesai" class="input-time-modern"
                        value="{{ $schedule->sore_selesai ?? '16:05' }}">
                </div>
            </div>
        </div>

        <!-- SMART-BACKUP -->
        <div class="schedule-card smart-backup-card">
            <div class="smart-backup-info">
                <div class="smart-backup-header">
                    <h3 class="card-title title-blue">Smart Backup</h3>
                    <div class="status-dot backup"></div>
                </div>
                <p class="smart-backup-desc">
                    Sistem cerdas: Pompa akan menyala otomatis jika kelembapan ruangan turun di bawah batas yang
                    ditentukan, meskipun di luar jadwal.
                </p>
            </div>

            <div class="smart-backup-control">
                <label>Batas Kelembapan Minimal:</label>
                <div class="smart-backup-input-wrapper">
                    <input type="number" name="batas_kelembapan" class="input-time-modern"
                        value="{{ $schedule->batas_kelembapan ?? 80 }}">
                    <span>%</span>
                </div>
            </div>
        </div>

        <!-- SAVE -->
        <div class="action-row">
            <button type="submit" class="btn-save">Simpan Konfigurasi</button>
        </div>
    </form>
@endsection
