<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Penyiraman | JAMKOT</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/panel.css') }}">
    <link rel="stylesheet" href="{{ asset('css/jadwal.css') }}">
</head>

<body>

    <div class="panel-layout">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>JAMKOT</h2>
            </div>

            <nav class="sidebar-nav">
                <a href="{{ route('panel') }}" class="nav-link">Panel Utama</a>
                <a href="{{ route('jadwal') }}"
                    class="nav-link {{ request()->routeIs('jadwal') ? 'active' : '' }}">Schedules</a>
            </nav>

            <div class="sidebar-footer">
                <span class="user-greeting">Halo, {{ auth()->user()->username ?? 'admin' }}</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-logout-sidebar">Logout</button>
                </form>
            </div>
        </aside>

        <div class="panel-content">
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
                            <input type="time" name="jadwal_pagi_mulai" value="08:00">
                        </div>
                        <div class="input-group">
                            <label>Jam Selesai</label>
                            <input type="time" name="jadwal_pagi_selesai" value="08:05">
                        </div>
                    </div>

                    <div class="summary-card">
                        <div class="card-title-wrapper">
                            <h3 class="card-title">Sesi Siang</h3>
                            <div class="status-dot siang"></div>
                        </div>
                        <div class="input-group">
                            <label>Jam Mulai</label>
                            <input type="time" name="jadwal_siang_mulai" value="12:00">
                        </div>
                        <div class="input-group">
                            <label>Jam Selesai</label>
                            <input type="time" name="jadwal_siang_selesai" value="12:05">
                        </div>
                    </div>

                    <div class="summary-card">
                        <div class="card-title-wrapper">
                            <h3 class="card-title">Sesi Sore</h3>
                            <div class="status-dot online"></div>
                        </div>
                        <div class="input-group">
                            <label>Jam Mulai</label>
                            <input type="time" name="jadwal_sore_mulai" value="16:00">
                        </div>
                        <div class="input-group">
                            <label>Jam Selesai</label>
                            <input type="time" name="jadwal_sore_selesai" value="16:05">
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
                                <input type="number" name="batas_kelembapan" value="80" min="0" max="100">
                                <span>%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="action-row">
                    <button type="submit" class="btn-save">Simpan Konfigurasi</button>
                </div>
            </form>

        </div>
    </div>

</body>

</html>