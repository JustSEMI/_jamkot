<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings | JAMKOT</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/panel.css') }}">
    <link rel="stylesheet" href="{{ asset('css/settings.css') }}">
    @vite('resources/js/app.js')
</head>

<body>

    <div class="panel-layout">

        <!-- SIDEBAR -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>JAMKOT</h2>
            </div>

            <nav class="sidebar-nav">
                <a href="{{ route('panel') }}" class="nav-link {{ Route::is('panel') ? 'active' : '' }}">
                    <i class="fa-solid fa-gauge-high"></i> Panel Utama
                </a>
                <a href="{{ route('analisis') }}" class="nav-link {{ Route::is('analisis') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-simple"></i> Analisis
                </a>
                <a href="{{ route('schedule') }}" class="nav-link {{ Route::is('schedule') ? 'active' : '' }}">
                    <i class="fa-solid fa-calendar-days"></i> Schedules
                </a>
                <a href="{{ route('settings.index') }}" class="nav-link {{ Route::is('settings.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-gear"></i> Settings
                </a>
            </nav>

            <div class="sidebar-footer">
                <span class="user-greeting">Halo, {{ auth()->user()->username ?? 'admin' }}</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-logout-sidebar">
                        <i class="fa-solid fa-right-from-bracket"></i> Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- KONTEN UTAMA -->
        <main class="panel-content">
            <header class="content-header-flex">
                <div>
                    <h1>PENGATURAN</h1>
                    <p>Manajemen data dan sistem JAMKOT.</p>
                </div>
            </header>

            <div class="settings-container">
                <div class="glow-card settings-card">
                    <h2 class="section-title" style="margin: 0 0 0.5rem 0; color: #ededed;">Manajemen Data Sensor</h2>
                    <p class="text-muted" style="margin-bottom: 2rem;">Kontrol riwayat pembacaan sensor pada sistem
                        database MariaDB Anda.</p>

                    <div class="danger-zone">
                        <div class="danger-header">
                            <span class="danger-icon">⚠️</span>
                            <h3>Zona Berbahaya</h3>
                        </div>
                        <p>Tindakan ini akan menghapus permanen seluruh riwayat suhu, kelembapan, dan status pompa dari
                            database. Aksi ini tidak dapat dibatalkan.</p>

                        <form id="resetForm" action="{{ route('settings.reset') }}" method="POST">
                            @csrf
                            <button type="button" class="btn-danger" onclick="bukaModal()">Reset Semua Data
                                Sensor</button>
                        </form>

                        <!-- MODAL -->
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

        </main>
    </div>

    <!-- TOAST NOTIFICATION -->
    @if(session('sukses'))
        <div id="toast-modern" class="toast-wrapper">
            <div class="toast-progress"></div>
            <div class="toast-body">
                <div class="toast-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                        stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </div>
                <div class="toast-text">
                    <h4>Success</h4>
                    <p>{{ session('sukses') }}</p>
                </div>
                <button class="toast-close" onclick="tutupToastModern()">×</button>
            </div>
        </div>
        <script src="{{ asset('js/toast.js') }}"></script>
    @endif

    <!-- MODAL SCRIPTS -->
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
</body>

</html>