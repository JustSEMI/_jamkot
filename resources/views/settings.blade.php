@extends('layouts.panel')

@section('title', 'Settings')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/settings.css') }}">
@endpush

@section('content')
    <header class="content-header">
        <h1>Pengaturan</h1>
        <p>Manajemen data dan sistem JAMKOT.</p>
    </header>

    <div class="settings-container">
        <div class="settings-card">
            <h2>Manajemen Data Sensor</h2>
            <p>Kontrol riwayat pembacaan sensor pada sistem database MariaDB Anda.</p>

            <div class="danger-zone">
                <h3>Zona Berbahaya</h3>
                <p>Tindakan ini akan menghapus permanen seluruh riwayat suhu, kelembapan, dan status pompa dari
                    database. Aksi ini tidak dapat dibatalkan.</p>

                <form id="resetForm" action="{{ route('settings.reset') }}" method="POST">
                    @csrf
                    <button type="button" class="btn-danger" onclick="bukaModal()">Reset Semua Data
                        Sensor</button>
                </form>

                <div id="modalReset" class="modal-overlay">
                    <div class="modal-box">
                        <div class="modal-icon">⚠️</div>
                        <h3 class="modal-title">Peringatan Keras!</h3>
                        <p class="modal-text">Apakah Anda yakin ingin menghapus SEMUA data riwayat suhu dan
                            kelembapan? Tindakan ini tidak bisa dibatalkan!</p>
                        <div class="modal-actions">
                            <button type="button" class="btn-cancel" onclick="tutupModal()">Batal</button>
                            <button type="button" class="btn-danger" onclick="gasReset()">Ya, Hapus
                                Semua!</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @if(session('sukseshapus'))
        <div id="toast-sukses" class="toast-notification">
            <span class="toast-text">{{ session('sukseshapus') }}</span>
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
    @endif
    <script>
        function bukaModal() {
            document.getElementById('modalReset').classList.add('active');
        }
        function tutupModal() {
            document.getElementById('modalReset').classList.remove('active');
        }
        function gasReset() {
            document.getElementById('resetForm').submit();
        }
    </script>
@endpush