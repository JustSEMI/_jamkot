<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- PREVENT FOUC & SETUP UI THEME -->
    <script>
        (function() {
            const uiVersion = localStorage.getItem('jamkot-ui-version') || 'v2';
            document.documentElement.setAttribute('data-ui-version', uiVersion);
        })();
    </script>
    <title>Kelola User | JAMKOT</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,500,0,0&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/panel.css') }}">
    <link rel="stylesheet" href="{{ asset('css/settings.css') }}">
    <link rel="stylesheet" href="{{ asset('css/mobile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/material3.css') }}">
    <style>
        /* ===== PERMISSION TABLE ===== */
        .users-table-wrapper {
            overflow-x: auto;
        }

        .users-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }

        .users-table th {
            text-align: left;
            padding: 0.75rem 0.5rem;
            font-size: 0.7rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border-color);
        }

        .users-table td {
            padding: 0.9rem 0.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.04);
            vertical-align: middle;
        }

        .users-table tr:last-child td { border-bottom: none; }

        .users-table tr:hover td {
            background: rgba(255,255,255,0.02);
        }

        .user-name-cell {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--warna-utama, #10b981), #6c63ff);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 600;
            color: #fff;
            flex-shrink: 0;
            text-transform: uppercase;
        }

        .user-info strong {
            display: block;
            color: var(--text-primary);
            font-weight: 500;
        }

        .user-info small {
            color: var(--text-muted);
            font-size: 0.75rem;
        }

        /* Checkbox Style */
        .custom-checkbox {
            position: relative;
            display: inline-flex;
            align-items: center;
            cursor: pointer;
        }

        .custom-checkbox input[type="checkbox"] {
            opacity: 0;
            width: 0;
            height: 0;
            position: absolute;
        }

        .checkmark {
            width: 20px;
            height: 20px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .custom-checkbox:hover .checkmark {
            border-color: rgba(255,255,255,0.4);
        }

        .custom-checkbox input:checked + .checkmark {
            background: var(--warna-utama, #10b981);
            border-color: var(--warna-utama, #10b981);
        }

        .checkmark::after {
            content: "";
            display: none;
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
            margin-bottom: 2px;
        }

        .custom-checkbox input:checked + .checkmark::after {
            display: block;
        }

        .perm-col {
            text-align: center;
        }

        .save-perm-btn {
            padding: 0.4rem 0.9rem;
            font-size: 0.78rem;
            border: none;
            border-radius: 8px;
            background: var(--warna-utama, #10b981);
            color: #111;
            cursor: pointer;
            font-family: inherit;
            font-weight: 600;
            transition: opacity 0.2s;
        }

        /* Material 3 Theme Overrides */
        html[data-ui-version="v1"] .custom-checkbox input:checked + .checkmark {
            background: var(--m3-primary) !important;
            border-color: var(--m3-primary) !important;
        }

        html[data-ui-version="v1"] .checkmark::after {
            border-color: var(--m3-on-primary) !important;
        }

        html[data-ui-version="v1"] .checkmark {
            border-color: var(--m3-outline) !important;
            border-radius: 6px !important;
        }

        html[data-ui-version="v1"] .users-table th {
            color: var(--m3-on-surface-variant) !important;
            border-bottom: 2px solid var(--m3-outline-variant) !important;
        }

        html[data-ui-version="v1"] .users-table td {
            border-bottom: 1px solid var(--m3-outline-variant) !important;
            color: var(--m3-on-surface) !important;
        }

        html[data-ui-version="v1"] .user-info strong {
            color: var(--m3-on-surface) !important;
        }

        html[data-ui-version="v1"] .user-info small {
            color: var(--m3-on-surface-variant) !important;
        }

        .save-perm-btn:hover { opacity: 0.85; }

        .badge-admin {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.25rem 0.6rem;
            border-radius: 100px;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            background: linear-gradient(135deg, #ff6b6b22, #ff6b6b44);
            color: #ff6b6b;
            border: 1px solid #ff6b6b44;
        }

        .badge-user {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.25rem 0.6rem;
            border-radius: 100px;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            background: linear-gradient(135deg, #3ecf8e22, #3ecf8e44);
            color: #3ecf8e;
            border: 1px solid #3ecf8e44;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--text-muted);
        }

        .empty-state .material-symbols-rounded {
            font-size: 3rem;
            display: block;
            margin-bottom: 0.75rem;
            opacity: 0.4;
        }

        /* Material 3 Adaptive Buttons Overrides - Expressive & Thumb-friendly */
        html[data-ui-version="v1"] .save-perm-btn {
            background: var(--m3-primary) !important;
            color: #111 !important;
            border-radius: 20px !important;
            font-family: var(--m3-font) !important;
            font-weight: 600 !important;
            padding: 0.5rem 1.25rem !important;
            font-size: 0.8rem !important;
            transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1) !important;
            box-shadow: 0 2px 6px rgba(128, 222, 197, 0.15) !important;
            border: none !important;
        }

        html[data-ui-version="v1"] .save-perm-btn:hover {
            background: var(--m3-primary) !important;
            transform: scale(1.05) !important;
            box-shadow: 0 6px 18px rgba(128, 222, 197, 0.3) !important;
            opacity: 1 !important;
        }

        html[data-ui-version="v1"] .save-perm-btn:active {
            transform: scale(0.95) !important;
        }

        @media (max-width: 839px) {
            html[data-ui-version="v1"] .save-perm-btn {
                padding: 0.65rem 1rem !important; /* Larger touch targets for mobile */
                font-size: 0.825rem !important;
                width: 100% !important;
                display: block !important;
                text-align: center !important;
            }
        }
    </style>
    @vite('resources/js/app.js')
</head>

<body>

    <div class="panel-layout">

        <!-- MOBILE NAV -->
        <header class="mobile-top-nav">
            <div class="mobile-logo">JAMKOT</div>
            <button class="btn-toggle-sidebar" id="sidebar-toggle">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="mobile-top-actions">
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-mobile-logout" title="Logout">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </button>
                </form>
            </div>
        </header>

        <div class="sidebar-overlay" id="sidebar-overlay"></div>

        <!-- SIDEBAR (ADMIN) -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h2>JAMKOT</h2>
            </div>

            <nav class="sidebar-nav">
                @if(auth()->user()->canAccess('panel'))
                <a href="{{ route('panel') }}" class="nav-link {{ Route::is('panel') ? 'active' : '' }}">
                    <i class="fa-solid fa-gauge"></i>
                    <span>Panel Utama</span>
                </a>
                @endif
                @if(auth()->user()->canAccess('analisis'))
                <a href="{{ route('analisis') }}" class="nav-link {{ Route::is('analisis') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-simple"></i>
                    <span>Analisis</span>
                </a>
                @endif
                @if(auth()->user()->canAccess('schedule'))
                <a href="{{ route('schedule') }}" class="nav-link {{ Route::is('schedule') ? 'active' : '' }}">
                    <i class="fa-solid fa-clock"></i>
                    <span>Schedules</span>
                </a>
                @endif
                @if(auth()->user()->canAccess('settings'))
                <a href="{{ route('settings.index') }}" class="nav-link {{ Route::is('settings.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-gear"></i>
                    <span>Settings</span>
                </a>
                @endif
                @if(auth()->user()->canAccess('view3d'))
                <a href="{{ route('view3d') }}" class="nav-link {{ Route::is('view3d') ? 'active' : '' }}">
                    <i class="fa-solid fa-cube"></i>
                    <span>3D View</span>
                </a>
                @endif
                @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.users') }}" class="nav-link nav-link-admin {{ Route::is('admin.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users-gear"></i>
                    <span>Kelola User</span>
                </a>
                @endif
            </nav>

            <div class="sidebar-footer">
                <span class="user-greeting">Halo, {{ auth()->user()->username ?? 'admin' }}</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-logout-sidebar" title="Logout">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- KONTEN UTAMA -->
        <main class="panel-content">
            <header class="content-header-flex">
                <div>
                    <h1>KELOLA USER</h1>
                    <p>Atur hak akses halaman untuk setiap pengguna sistem JAMKOT.</p>
                </div>
            </header>

            @if(session('sukses'))
                <div id="toast-modern" class="toast-wrapper">
                    <div class="toast-progress"></div>
                    <div class="toast-body">
                        <div class="toast-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </div>
                        <div class="toast-text">
                            <h4>Berhasil</h4>
                            <p>{{ session('sukses') }}</p>
                        </div>
                        <button class="toast-close" onclick="tutupToastModern()">×</button>
                    </div>
                </div>
                <script src="{{ asset('js/toast.js') }}"></script>
            @endif

            @if(session('error'))
                <div style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); border-radius: 10px; padding: 1rem 1.25rem; margin-bottom: 1.5rem; color: #f87171; font-size: 0.875rem;">
                    <i class="fa-solid fa-circle-exclamation" style="margin-right: 0.5rem;"></i>{{ session('error') }}
                </div>
            @endif

            <div class="settings-container">
                <div class="glow-card settings-card">
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                        <span class="material-symbols-rounded" style="color: var(--warna-utama, #10b981);">manage_accounts</span>
                        <h2 class="section-title" style="margin: 0; color: #ededed;">Daftar Pengguna</h2>
                    </div>
                    <p class="text-muted" style="margin-bottom: 2rem; font-size: 0.85rem;">
                        Centang atau hapus centang pada setiap kolom untuk mengatur akses halaman. Klik <strong>Simpan</strong> pada baris yang ingin Anda perbarui.
                    </p>

                    <div class="users-table-wrapper">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th>PENGGUNA</th>
                                    <th class="perm-col">Panel</th>
                                    <th class="perm-col">Analisis</th>
                                    <th class="perm-col">Schedules</th>
                                    <th class="perm-col">3D View</th>
                                    <th class="perm-col">Settings</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td>
                                            <div class="user-name-cell">
                                                <div class="user-info">
                                                    <strong>{{ $user->username }}</strong>
                                                    <small>{{ $user->email }}</small>
                                                </div>
                                            </div>
                                        </td>

                                        @php 
                                            $perms = ['panel', 'analisis', 'schedule', 'view3d', 'settings']; 
                                            $formId = 'form_user_' . $user->id;
                                        @endphp

                                        @foreach($perms as $perm)
                                            <td class="perm-col">
                                                <label class="custom-checkbox">
                                                    <input type="checkbox" name="can_{{ $perm }}" value="1" form="{{ $formId }}"
                                                        {{ $user->{"can_{$perm}"} ? 'checked' : '' }}>
                                                    <span class="checkmark"></span>
                                                </label>
                                            </td>
                                        @endforeach
                                        <td>
                                            <button type="submit" form="{{ $formId }}" class="save-perm-btn btn-sm">Simpan</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">
                                            <div class="empty-state">
                                                <span class="material-symbols-rounded">group</span>
                                                Belum ada user terdaftar.
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Formulir tersembunyi di luar tabel -->
            @foreach($users as $user)
                <form id="form_user_{{ $user->id }}" action="{{ route('admin.users.permissions', $user) }}" method="POST" style="display: none;">
                    @csrf
                </form>
            @endforeach

        </main>
    </div>

    <script src="{{ asset('js/sidebar.js') }}"></script>

    <!-- BOTTOM NAV FOR MOBILE (M3 Only) -->
    <nav class="bottom-nav">
        @if(auth()->user()->canAccess('panel'))
        <a href="{{ route('panel') }}" class="bottom-nav-link {{ Route::is('panel') ? 'active' : '' }}">
            <div class="bottom-nav-icon-wrapper">
                <i class="fa-solid fa-gauge"></i>
            </div>
            <span>Panel</span>
        </a>
        @endif
        @if(auth()->user()->canAccess('analisis'))
        <a href="{{ route('analisis') }}" class="bottom-nav-link {{ Route::is('analisis') ? 'active' : '' }}">
            <div class="bottom-nav-icon-wrapper">
                <i class="fa-solid fa-chart-simple"></i>
            </div>
            <span>Analisis</span>
        </a>
        @endif
        @if(auth()->user()->canAccess('schedule'))
        <a href="{{ route('schedule') }}" class="bottom-nav-link {{ Route::is('schedule') ? 'active' : '' }}">
            <div class="bottom-nav-icon-wrapper">
                <i class="fa-solid fa-clock"></i>
            </div>
            <span>Schedule</span>
        </a>
        @endif
        @if(auth()->user()->isAdmin())
        <a href="{{ route('admin.users') }}" class="bottom-nav-link {{ Route::is('admin.*') ? 'active' : '' }}">
            <div class="bottom-nav-icon-wrapper">
                <i class="fa-solid fa-users-gear"></i>
            </div>
            <span>Users</span>
        </a>
        @else
        <a href="{{ route('settings.index') }}" class="bottom-nav-link {{ Route::is('settings.*') ? 'active' : '' }}">
            <div class="bottom-nav-icon-wrapper">
                <i class="fa-solid fa-gear"></i>
            </div>
            <span>Settings</span>
        </a>
        @endif
        @if(auth()->user()->canAccess('view3d'))
        <a href="{{ route('view3d') }}" class="bottom-nav-link {{ Route::is('view3d') ? 'active' : '' }}">
            <div class="bottom-nav-icon-wrapper">
                <i class="fa-solid fa-cube"></i>
            </div>
            <span>3D View</span>
        </a>
        @endif
    </nav>
</body>

</html>
