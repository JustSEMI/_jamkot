@extends('layouts.app')

@section('title', 'Reset Data')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/settings.css') }}?v={{ filemtime(public_path('css/pages/settings.css')) }}">
@endpush

@section('content')
    <header class="content-header-flex">
        <div>
            <h1 style="color: var(--warna-utama, #10b981); text-transform: none;">RESET DATA SENSOR</h1>
            <p style="text-transform: none;">Manajemen data dan pembersihan sistem JAMKOT.</p>
        </div>

        <!-- Jam & Tanggal -->
        <div class="datetime-widget">
            <div id="realtime-clock" class="time-display">00:00:00</div>
            <div id="realtime-date" class="date-display">Memuat...</div>
        </div>
    </header>

    <div class="settings-container" style="margin-top: 1.5rem;">
        <!-- Manajemen Data Sensor -->
        <div class="glow-card settings-card">
            <h2 class="section-title" style="margin: 0 0 0.5rem 0; color: #ededed;">Manajemen Data Sensor</h2>
            <p class="text-muted" style="margin-bottom: 2rem;">Kontrol riwayat pembacaan sensor pada sistem database MariaDB Anda.</p>

            <div class="danger-zone">
                <div class="danger-header">
                    <span class="danger-icon material-symbols-rounded">warning</span>
                    <h3>Zona Berbahaya</h3>
                </div>
                <p>Tindakan ini akan menghapus permanen seluruh riwayat suhu, kelembapan, dan status pompa dari database. Aksi ini tidak dapat dibatalkan.</p>

                <form id="resetForm" action="{{ route('settings.reset') }}" method="POST">
                    @csrf
                    <button type="button" class="btn-danger" onclick="bukaModal()">Reset Semua Data Sensor</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('modals')
    <!-- MODAL RESET -->
    <div id="modalReset" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-icon-wrapper">
                <i class="fa-solid fa-trash-can"></i>
            </div>
            <h3 class="modal-title">Reset Semua Data Sensor?</h3>
            <p class="modal-subtitle">Tindakan ini akan menghapus permanen seluruh record sensor — suhu, kelembapan, intensitas cahaya, dan status pompa.</p>
            
            <div class="modal-badges">
                <span class="modal-badge"><i class="fa-solid fa-chart-line"></i> Grafik akan kosong</span>
                <span class="modal-badge"><i class="fa-solid fa-table"></i> Tabel akan kosong</span>
                <span class="modal-badge"><i class="fa-solid fa-circle-xmark"></i> Tidak dapat dikembalikan</span>
            </div>

            <div class="modal-input-group">
                <label for="confirm-reset">Ketik <strong>RESET</strong> untuk konfirmasi:</label>
                <input type="text" id="confirm-reset" placeholder="Ketik RESET di sini..." oninput="checkResetInput(this.value)">
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="tutupModal()">Batalkan</button>
                <button type="button" class="btn-danger" id="btn-confirm-reset" onclick="gasReset()" disabled>Hapus Permanen</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/utils/clock.js') }}"></script>
    <script>
        function bukaModal() {
            document.getElementById('modalReset').classList.add('active');
        }
        function tutupModal() {
            document.getElementById('modalReset').classList.remove('active');
            document.getElementById('confirm-reset').value = '';
            document.getElementById('btn-confirm-reset').setAttribute('disabled', 'true');
            document.getElementById('btn-confirm-reset').classList.remove('danger-active');
        }
        function checkResetInput(val) {
            const btn = document.getElementById('btn-confirm-reset');
            if (val === 'RESET') {
                btn.removeAttribute('disabled');
                btn.classList.add('danger-active');
            } else {
                btn.setAttribute('disabled', 'true');
                btn.classList.remove('danger-active');
            }
        }
        function gasReset() {
            document.getElementById('resetForm').submit();
        }
    </script>
@endpush
