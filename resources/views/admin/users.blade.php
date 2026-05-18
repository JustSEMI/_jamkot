<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- PREVENT FOUC & SETUP UI THEME -->
    <script>
        (function() {
            const uiVersion = localStorage.getItem('jamkot-ui-version') || 'v1';
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
            min-width: 800px; /* Prevent column squishing and text/button clipping */
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
            text-align: center !important;
        }

        .save-perm-btn {
            background: var(--warna-utama, #10b981) !important;
            color: #111 !important;
            border-radius: 0.375rem !important; /* Default to Neon Glow 6px rounded */
            font-family: inherit !important;
            font-weight: 600 !important;
            padding: 0.45rem 1.1rem !important;
            font-size: 0.78rem !important;
            border: 1px solid var(--warna-utama, #10b981) !important;
            box-shadow: 0 0 10px rgba(16, 185, 129, 0.1) !important;
            transition: all 0.2s ease-in-out !important;
            cursor: pointer;
        }

        .save-perm-btn:hover {
            background: transparent !important;
            color: var(--warna-utama, #10b981) !important;
            box-shadow: 0 0 15px rgba(16, 185, 129, 0.4) !important;
            transform: translateY(-2px) !important;
            opacity: 1 !important;
        }

        .save-perm-btn:active {
            transform: translateY(0) !important;
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

        html[data-ui-version="v1"] .btn-delete-user {
            background: var(--m3-error-container) !important;
            color: var(--m3-on-error-container) !important;
            border-radius: 20px !important;
            font-family: var(--m3-font) !important;
            font-weight: 600 !important;
            padding: 0.5rem 0.8rem !important;
            font-size: 0.8rem !important;
            border: none !important;
            transition: all 0.2s ease !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        html[data-ui-version="v1"] .btn-delete-user:hover {
            background: #ffb4ab !important; /* M3 Error fixed variant */
            transform: scale(1.1);
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

        /* Neon Glow (v2) Theme Overrides */
        html[data-ui-version="v2"] .checkmark {
            border-color: rgba(255, 255, 255, 0.2) !important;
            background: rgba(255, 255, 255, 0.02) !important;
            border-radius: 4px !important;
        }

        html[data-ui-version="v2"] .custom-checkbox:hover .checkmark {
            border-color: var(--warna-utama, #10b981) !important;
            box-shadow: 0 0 8px rgba(16, 185, 129, 0.2) !important;
        }

        html[data-ui-version="v2"] .custom-checkbox input:checked + .checkmark {
            background: rgba(16, 185, 129, 0.12) !important;
            border-color: var(--warna-utama, #10b981) !important;
            box-shadow: 0 0 12px rgba(16, 185, 129, 0.4) !important;
        }

        html[data-ui-version="v2"] .checkmark::after {
            border-color: var(--warna-utama, #10b981) !important;
        }

        html[data-ui-version="v2"] .btn-delete-user {
            background: rgba(239, 68, 68, 0.1) !important;
            color: #ef4444 !important;
            border: 1px solid rgba(239, 68, 68, 0.2) !important;
            border-radius: 8px !important;
            padding: 0.5rem 0.8rem !important;
            transition: all 0.2s ease !important;
        }

        html[data-ui-version="v2"] .btn-delete-user:hover {
            background: rgba(239, 68, 68, 0.2) !important;
            box-shadow: 0 0 10px rgba(239, 68, 68, 0.3) !important;
        }

        /* CUSTOM MODAL THEME SYNC */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .custom-modal {
            background: #1a1a1a;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 28px;
            padding: 2rem;
            max-width: 400px;
            width: 90%;
            text-align: center;
            transform: scale(0.9);
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .modal-overlay.active .custom-modal {
            transform: scale(1);
        }

        .modal-icon-wrapper {
            width: 72px;
            height: 72px;
            background: rgba(239, 68, 68, 0.15);
            color: #ef4444;
            border-radius: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }

        .modal-icon-wrapper .material-symbols-rounded {
            font-size: 36px;
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #ededed;
        }

        .modal-text {
            color: #9ca3af;
            font-size: 0.9rem;
            line-height: 1.5;
            margin-bottom: 2rem;
        }

        .modal-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }

        .btn-modal {
            padding: 0.75rem 1.5rem;
            border-radius: 100px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
            font-size: 0.9rem;
        }

        .btn-modal-cancel {
            background: rgba(255, 255, 255, 0.05);
            color: #ededed;
        }

        .btn-modal-confirm {
            background: #ef4444;
            color: white;
        }

        /* M3 Overrides */
        html[data-ui-version="v1"] .custom-modal {
            background: var(--m3-surface-container-high) !important;
            font-family: var(--m3-font) !important;
        }
        html[data-ui-version="v1"] .modal-title { color: var(--m3-on-surface) !important; }
        html[data-ui-version="v1"] .modal-text { color: var(--m3-on-surface-variant) !important; }

        html[data-ui-version="v2"] .custom-modal {
            border-color: rgba(239, 68, 68, 0.3) !important;
            box-shadow: 0 0 30px rgba(239, 68, 68, 0.15) !important;
        }

        html[data-ui-version="v2"] .users-table th {
            color: #6b7280 !important;
            font-weight: 500 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            font-size: 0.75rem !important;
            border-bottom: 1px solid #262626 !important;
        }

        html[data-ui-version="v2"] .users-table td {
            border-bottom: 1px solid rgba(255, 255, 255, 0.02) !important;
            color: #ededed !important;
        }

        /* Icon theme sync */
        html[data-ui-version="v1"] .icon-neon-only { display: none !important; }
        html[data-ui-version="v2"] .icon-m3-only { display: none !important; }

        /* Expand container on desktop to prevent horizontal scrolling/sliding and clipping on both themes */
        @media (min-width: 769px) {
            .settings-container {
                max-width: 1100px !important;
                width: 100%;
            }
            .users-table-wrapper {
                overflow-x: auto !important; /* Enable smooth scrolling on intermediate desktop sizes */
            }
            /* Give more horizontal breathing room for wide tables on desktop */
            .panel-content {
                padding: 2rem 1.5rem !important;
            }
        }

        @media (min-width: 1024px) {
            .users-table-wrapper {
                overflow-x: visible !important; /* Fully lock static when screen is wide enough */
            }
        }

        /* --- Mobile Responsive Scroll Force --- */
        @media (max-width: 768px) {
            .panel-content {
                overflow-x: hidden !important;
                padding: 1rem !important;
            }

            .settings-container {
                margin-top: 1rem !important;
                width: 100% !important;
                padding: 0 !important;
            }

            .settings-card {
                padding: 1.25rem !important;
                overflow: hidden !important; /* Contain the scroll within card if possible, or use visible */
            }

            .users-table-wrapper {
                display: block !important;
                width: 100% !important;
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch !important;
                margin: 0 -0.5rem !important; /* Slight negative margin to show more content */
                padding: 0 0.5rem 15px 0.5rem !important;
            }
            
            .users-table {
                min-width: 1000px !important; /* Force extra width for many columns */
                width: 100% !important;
            }

            .users-table th, .users-table td {
                white-space: nowrap !important;
                padding: 1rem 0.75rem !important;
            }
            
            /* Custom scrollbar for better visibility */
            .users-table-wrapper::-webkit-scrollbar {
                height: 8px !important;
                display: block !important;
            }
            .users-table-wrapper::-webkit-scrollbar-thumb {
                background: var(--warna-utama, #10b981) !important;
                border-radius: 10px !important;
            }
            .users-table-wrapper::-webkit-scrollbar-track {
                background: rgba(255, 255, 255, 0.05) !important;
            }
        }

        /* --- USER CARDS GRID --- */
        .user-cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 1.25rem;
            margin-top: 1.5rem;
        }

        .user-accordion-card {
            padding: 1.25rem !important;
            background: #181818 !important; /* Solid dark background for consistency */
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            border-radius: 16px !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            height: fit-content !important;
        }

        .user-accordion-card:hover {
            transform: translateY(-4px);
            background: rgba(255, 255, 255, 0.04) !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
        }

        @media (max-width: 900px) {
            .user-cards-container {
                grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            }
        }

        @media (max-width: 600px) {
            .user-cards-container {
                grid-template-columns: 1fr;
            }
        }

        /* --- PERMS FOOTER RESPONSIVE --- */
        .perms-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
        }

        @media (max-width: 600px) {
            .perms-footer {
                flex-direction: column;
                gap: 0.75rem;
            }
            .perms-footer button {
                width: 100% !important;
                display: flex !important;
                justify-content: center !important;
                align-items: center !important;
                padding: 0.8rem !important; /* Larger touch target */
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
                @if(auth()->user()->canAccess('admin'))
                    @if(Route::is('settings.index'))
                    <a href="{{ route('panel') }}" class="btn-mobile-settings" title="Back to Panel">
                        <i class="fa-solid fa-house"></i>
                    </a>
                    @else
                    <a href="{{ route('settings.index') }}" class="btn-mobile-settings" title="Settings">
                        <i class="fa-solid fa-gear"></i>
                    </a>
                    @endif
                @endif
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
                @if(auth()->user()->canAccess('admin'))
                <a href="{{ route('admin.users') }}" class="nav-link nav-link-admin {{ Route::is('admin.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users-gear"></i>
                    <span>Admin</span>
                </a>
                @endif
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
                        <!-- Theme-aligned Icons -->
                        <span class="material-symbols-rounded icon-m3-only" style="color: var(--warna-utama, #10b981);">manage_accounts</span>
                        <i class="fa-solid fa-users icon-neon-only" style="color: var(--warna-utama, #10b981); font-size: 1.2rem;"></i>
                        <h2 class="section-title" style="margin: 0; color: #ededed;">Daftar Pengguna</h2>
                    </div>
                    <p class="text-muted" style="margin-bottom: 2rem; font-size: 0.85rem;">
                        Centang atau hapus centang pada setiap kolom untuk mengatur akses halaman. Klik <strong>Simpan</strong> pada baris yang ingin Anda perbarui.
                    </p>

                    <div class="user-cards-container">
                        @forelse($users as $user)
                            <div class="user-accordion-card">
                                <!-- Header Card -->
                                <div class="user-card-header" style="display: flex; justify-content: space-between; align-items: center; cursor: pointer;" onclick="togglePerms('{{ $user->id }}')">
                                    <div class="user-info" style="display: flex; flex-direction: column;">
                                        <strong style="font-size: 1.1rem; color: #ededed;">{{ $user->username }}</strong>
                                        <small style="color: #9ca3af;">{{ $user->email }}</small>
                                    </div>
                                    <div class="user-card-actions">
                                        <button type="button" class="btn-toggle-perms" style="background: transparent; border: 1px solid rgba(255,255,255,0.2); color: #ededed; padding: 0.5rem 1rem; border-radius: 8px; cursor: pointer; transition: all 0.3s;">
                                            <i class="fa-solid fa-sliders" style="margin-right: 0.5rem;"></i> Kelola Akses
                                        </button>
                                    </div>
                                </div>

                                <!-- Body Card (Hidden by default) -->
                                <div class="user-card-body" id="perms_{{ $user->id }}" style="display: none; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid rgba(255,255,255,0.1);">
                                    <form action="{{ route('admin.users.permissions', $user) }}" method="POST">
                                        @csrf
                                        <div class="perms-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
                                            @php 
                                                $perms = [
                                                    'panel' => 'Panel Utama',
                                                    'analisis' => 'Analisis',
                                                    'schedule' => 'Schedules',
                                                    'view3d' => '3D View',
                                                    'settings' => 'Settings',
                                                    'admin' => 'Kelola User'
                                                ]; 
                                            @endphp

                                            @foreach($perms as $key => $label)
                                                <div class="perm-item" style="display: flex; align-items: center; justify-content: space-between; background: rgba(0,0,0,0.2); padding: 0.75rem; border-radius: 8px;">
                                                    <span style="font-size: 0.9rem;">{{ $label }}</span>
                                                    <label class="custom-checkbox">
                                                        <input type="checkbox" name="can_{{ $key }}" value="1" {{ $user->{"can_{$key}"} ? 'checked' : '' }}>
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="perms-footer">
                                            <button type="submit" class="save-perm-btn" style="padding: 0.6rem 1.5rem; border-radius: 8px; font-weight: 500;">
                                                <i class="fa-solid fa-floppy-disk" style="margin-right: 0.5rem;"></i> Simpan Perubahan
                                            </button>
                                            
                                            <button type="button" class="btn-delete-user" title="Hapus User" onclick="confirmDelete('{{ $user->id }}', '{{ $user->username }}')" style="padding: 0.6rem 1rem; border-radius: 8px;">
                                                <i class="fa-solid fa-trash-can"></i> Hapus
                                            </button>
                                        </div>
                                    </form>
                                    
                                    <form id="delete_form_{{ $user->id }}" action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="empty-state" style="text-align: center; padding: 3rem; color: #9ca3af;">
                                <span class="material-symbols-rounded" style="font-size: 3rem; opacity: 0.5; margin-bottom: 1rem;">group</span>
                                <p>Belum ada user terdaftar.</p>
                            </div>
                        @endforelse
                    </div>

                    <script>
                        function togglePerms(userId) {
                            const body = document.getElementById('perms_' + userId);
                            if (body.style.display === 'none' || body.style.display === '') {
                                body.style.display = 'block';
                            } else {
                                body.style.display = 'none';
                            }
                        }
                    </script>

            <!-- CUSTOM DELETE MODAL -->
            <div id="deleteModal" class="modal-overlay">
                <div class="custom-modal">
                    <div class="modal-icon-wrapper">
                        <span class="material-symbols-rounded">warning</span>
                    </div>
                    <h3 class="modal-title">Konfirmasi Hapus</h3>
                    <p class="modal-text">Apakah Anda yakin ingin menghapus user <strong id="deleteTargetName"></strong>? <br>Tindakan ini akan menghapus akun secara permanen.</p>
                    <div class="modal-actions">
                        <button type="button" class="btn-modal btn-modal-cancel" onclick="closeDeleteModal()">Batal</button>
                        <button type="button" class="btn-modal btn-modal-confirm" id="confirmDeleteBtn">Hapus Sekarang</button>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <script src="{{ asset('js/sidebar.js') }}"></script>
    <script>
        let currentDeleteId = null;

        function confirmDelete(userId, username) {
            currentDeleteId = userId;
            document.getElementById('deleteTargetName').innerText = username;
            document.getElementById('deleteModal').classList.add('active');
            
            document.getElementById('confirmDeleteBtn').onclick = function() {
                document.getElementById('delete_form_' + currentDeleteId).submit();
            };
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.remove('active');
        }

        // Close on escape
        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeDeleteModal();
        });
        
        // Close on click outside
        document.getElementById('deleteModal').addEventListener('click', (e) => {
            if (e.target === document.getElementById('deleteModal')) closeDeleteModal();
        });
    </script>

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
        @if(auth()->user()->canAccess('admin'))
        <a href="{{ route('admin.users') }}" class="bottom-nav-link {{ Route::is('admin.*') ? 'active' : '' }}">
            <div class="bottom-nav-icon-wrapper">
                <i class="fa-solid fa-users-gear"></i>
            </div>
            <span>Admin</span>
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
