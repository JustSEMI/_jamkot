<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings | JAMKOT</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/panel.css') }}">
    <link rel="stylesheet" href="{{ asset('css/settings.css') }}">
</head>

<body>

    <div class="panel-layout">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>JAMKOT</h2>
            </div>

            <nav class="sidebar-nav">
                <a href="{{ route('panel') }}" class="nav-link">Panel Utama</a>
                <a href="{{ route('jadwal') }}" class="nav-link">Schedules</a>
                <a href="{{ route('settings.index') }}" class="nav-link active">Settings</a>
            </nav>

            <div class="sidebar-footer">
                <span class="user-greeting">Halo, {{ auth()->user()->username ?? 'admin' }}</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-logout-sidebar">Logout</button>
                </form>
            </div>
        </aside>

        <main class="panel-content">
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

        </main>
    </div>

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
</body>


</html>