@extends('layouts.app')

@section('title', 'Edit Akses User')

@push('styles')
<style>
    .edit-layout {
        display: flex;
        gap: 2rem;
        margin-top: 1.5rem;
    }

    .profile-sidebar-card {
        flex: 0 0 280px;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        padding: 2.5rem 1.5rem;
        background: #111111;
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 16px;
        height: fit-content;
    }

    .profile-avatar {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: rgba(16, 185, 129, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.2);
        color: #10b981;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 1.25rem;
    }

    .profile-username {
        font-size: 1.25rem;
        font-weight: 600;
        color: #ffffff;
        margin-bottom: 0.35rem;
    }

    .profile-email {
        font-size: 0.85rem;
        color: #6b7280;
        margin-bottom: 0.5rem;
        word-break: break-all;
    }

    .self-account-badge {
        font-size: 0.75rem;
        color: #6b7280;
        margin-top: 1rem;
        display: inline-block;
        font-style: italic;
    }

    .form-content-card {
        flex: 1;
        padding: 2.5rem;
        background: #111111;
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 16px;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: #ededed;
        text-decoration: none;
        border: 1px solid rgba(255, 255, 255, 0.1);
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 600;
        background: rgba(255, 255, 255, 0.02);
        transition: all 0.2s;
    }

    .btn-back:hover {
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(255, 255, 255, 0.2);
        color: #ffffff;
    }

    .section-title-small {
        font-size: 0.75rem;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 0.75rem;
    }

    .role-cards-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

    .role-select-card {
        background: #151515;
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 12px;
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 1.25rem;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .role-select-card:hover {
        border-color: rgba(255, 255, 255, 0.15);
        background: rgba(255, 255, 255, 0.02);
    }

    .role-select-card.active {
        border-color: rgba(16, 185, 129, 0.5);
        background: rgba(16, 185, 129, 0.05);
        box-shadow: 0 0 15px rgba(16, 185, 129, 0.05);
    }

    .role-select-card.active#role-card-admin {
        border-color: rgba(16, 185, 129, 0.5);
    }

    .role-select-card.active#role-card-user {
        border-color: rgba(167, 139, 250, 0.5);
        background: rgba(167, 139, 250, 0.05);
    }

    .role-icon-box {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.02);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        color: #6b7280;
        transition: all 0.3s ease;
        flex-shrink: 0;
    }

    .role-select-card.active#role-card-admin .role-icon-box {
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
    }

    .role-select-card.active#role-card-user .role-icon-box {
        background: rgba(167, 139, 250, 0.1);
        color: #a78bfa;
    }

    .role-info h4 {
        font-size: 1rem;
        font-weight: 600;
        color: #ffffff;
        margin-bottom: 0.15rem;
    }

    .role-info p {
        font-size: 0.8rem;
        color: #6b7280;
    }

    .admin-info-banner {
        background: rgba(16, 185, 129, 0.08);
        border: 1px solid rgba(16, 185, 129, 0.15);
        color: #10b981;
        padding: 1rem 1.25rem;
        border-radius: 10px;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.875rem;
    }

    .admin-info-banner i {
        font-size: 1rem;
    }

    .permissions-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .perm-toggle-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #151515;
        border: 1px solid rgba(255, 255, 255, 0.05);
        padding: 1rem 1.5rem;
        border-radius: 12px;
        gap: 1.5rem;
    }

    .perm-info h4 {
        font-size: 0.95rem;
        font-weight: 600;
        color: #ffffff;
        margin-bottom: 0.2rem;
    }

    .perm-info p {
        font-size: 0.8rem;
        color: #6b7280;
        line-height: 1.4;
    }

    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 24px;
        flex-shrink: 0;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(255, 255, 255, 0.05);
        transition: .3s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 24px;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 3px;
        bottom: 3px;
        background-color: #9ca3af;
        transition: .3s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 50%;
    }

    .toggle-switch input:checked + .toggle-slider {
        background-color: rgba(16, 185, 129, 0.15);
        border-color: #10b981;
    }

    .toggle-switch input:checked + .toggle-slider:before {
        transform: translateX(20px);
        background-color: #10b981;
    }

    .form-action-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 2.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid rgba(255, 255, 255, 0.05);
    }

    .footer-note {
        font-size: 0.85rem;
        color: #6b7280;
    }

    .btn-save {
        background: #10b981;
        color: #0d0d11;
        border: none;
        padding: 0.75rem 2rem;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-save:hover {
        background: #059669;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
    }

    .btn-save:active {
        transform: translateY(0);
    }

    @media (max-width: 768px) {
        .edit-layout {
            flex-direction: column;
        }
        .profile-sidebar-card {
            width: 100%;
        }
        .role-cards-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
    }

    /* --- MATERIAL 3 EXPRESSIVE OVERRIDES --- */
    html[data-ui-version="v1"] .btn-back {
        padding: 0.65rem 1.35rem !important;
        border-radius: 100px !important;
        border: 1px solid var(--m3-outline) !important;
        background: transparent !important;
        color: var(--m3-primary) !important;
        font-family: var(--m3-font) !important;
        font-weight: 600 !important;
        transition: all 0.2s cubic-bezier(0.2, 0.8, 0.2, 1) !important;
    }

    html[data-ui-version="v1"] .btn-back:hover {
        background: var(--m3-primary-container) !important;
        color: var(--m3-on-primary-container) !important;
        border-color: var(--m3-primary) !important;
    }

    html[data-ui-version="v1"] .profile-sidebar-card {
        background: var(--m3-surface-container) !important;
        border: 1px solid var(--m3-outline-variant) !important;
        border-radius: 24px !important;
        padding: 2.5rem 1.75rem !important;
    }

    html[data-ui-version="v1"] .profile-avatar {
        background: var(--m3-primary-container) !important;
        color: var(--m3-on-primary-container) !important;
        border: 1px solid var(--m3-primary) !important;
    }

    html[data-ui-version="v1"] .profile-username {
        color: var(--m3-on-surface) !important;
        font-family: var(--m3-font) !important;
    }

    html[data-ui-version="v1"] .profile-email {
        color: var(--m3-on-surface-variant) !important;
    }

    html[data-ui-version="v1"] .self-account-badge {
        color: var(--m3-on-surface-variant) !important;
    }

    html[data-ui-version="v1"] .form-content-card {
        background: var(--m3-surface-container) !important;
        border: 1px solid var(--m3-outline-variant) !important;
        border-radius: 24px !important;
        padding: 2.5rem !important;
    }

    html[data-ui-version="v1"] .section-title-small {
        color: var(--m3-primary) !important;
        font-family: var(--m3-font) !important;
        font-weight: 700 !important;
        letter-spacing: 0.08em !important;
    }

    html[data-ui-version="v1"] .role-select-card {
        background: var(--m3-surface-container-low) !important;
        border: 1px solid var(--m3-outline-variant) !important;
        border-radius: 20px !important;
        transition: all 0.25s cubic-bezier(0.2, 0.8, 0.2, 1) !important;
    }

    html[data-ui-version="v1"] .role-select-card:hover {
        background: var(--m3-surface-container-high) !important;
        border-color: var(--m3-outline) !important;
    }

    html[data-ui-version="v1"] .role-select-card.active#role-card-admin {
        background: var(--m3-primary-container) !important;
        border-color: var(--m3-primary) !important;
    }

    html[data-ui-version="v1"] .role-select-card.active#role-card-user {
        background: var(--m3-secondary-container) !important;
        border-color: var(--m3-secondary) !important;
    }

    html[data-ui-version="v1"] .role-icon-box {
        background: var(--m3-surface-container-highest) !important;
        color: var(--m3-on-surface-variant) !important;
        border-radius: 12px !important;
    }

    html[data-ui-version="v1"] .role-select-card.active#role-card-admin .role-icon-box {
        background: var(--m3-primary) !important;
        color: var(--m3-on-primary) !important;
    }

    html[data-ui-version="v1"] .role-select-card.active#role-card-user .role-icon-box {
        background: var(--m3-secondary) !important;
        color: var(--m3-on-secondary) !important;
    }

    html[data-ui-version="v1"] .role-info h4 {
        color: var(--m3-on-surface) !important;
        font-family: var(--m3-font) !important;
    }

    html[data-ui-version="v1"] .role-select-card.active#role-card-admin .role-info h4 {
        color: var(--m3-on-primary-container) !important;
    }

    html[data-ui-version="v1"] .role-select-card.active#role-card-admin .role-info p {
        color: var(--m3-on-primary-container) !important;
        opacity: 0.8;
    }

    html[data-ui-version="v1"] .role-select-card.active#role-card-user .role-info h4 {
        color: var(--m3-on-secondary-container) !important;
    }

    html[data-ui-version="v1"] .role-select-card.active#role-card-user .role-info p {
        color: var(--m3-on-secondary-container) !important;
        opacity: 0.8;
    }

    html[data-ui-version="v1"] .role-info p {
        color: var(--m3-on-surface-variant) !important;
    }

    html[data-ui-version="v1"] .admin-info-banner {
        background: var(--m3-primary-container) !important;
        border: 1px solid var(--m3-primary) !important;
        color: var(--m3-on-primary-container) !important;
        border-radius: 16px !important;
        padding: 1.15rem 1.5rem !important;
        font-family: var(--m3-font) !important;
        font-weight: 500 !important;
    }

    html[data-ui-version="v1"] .perm-toggle-row {
        background: var(--m3-surface-container-low) !important;
        border: 1px solid var(--m3-outline-variant) !important;
        border-radius: 16px !important;
        padding: 1.15rem 1.5rem !important;
        transition: all 0.2s ease !important;
    }

    html[data-ui-version="v1"] .perm-toggle-row:hover {
        background: var(--m3-surface-container-high) !important;
    }

    html[data-ui-version="v1"] .perm-info h4 {
        color: var(--m3-on-surface) !important;
        font-family: var(--m3-font) !important;
    }

    html[data-ui-version="v1"] .perm-info p {
        color: var(--m3-on-surface-variant) !important;
    }

    html[data-ui-version="v1"] .toggle-slider {
        background-color: var(--m3-surface-container-highest) !important;
        border: 1px solid var(--m3-outline-variant) !important;
    }

    html[data-ui-version="v1"] .toggle-slider:before {
        background-color: var(--m3-outline) !important;
    }

    html[data-ui-version="v1"] .toggle-switch input:checked + .toggle-slider {
        background-color: var(--m3-primary-container) !important;
        border-color: var(--m3-primary) !important;
    }

    html[data-ui-version="v1"] .toggle-switch input:checked + .toggle-slider:before {
        background-color: var(--m3-primary) !important;
    }

    html[data-ui-version="v1"] .form-action-footer {
        border-top: 1px solid var(--m3-outline-variant) !important;
    }

    html[data-ui-version="v1"] .footer-note {
        color: var(--m3-on-surface-variant) !important;
    }

    html[data-ui-version="v1"] .btn-save {
        background: var(--m3-primary) !important;
        color: var(--m3-on-primary) !important;
        border-radius: 100px !important;
        padding: 0.75rem 2.25rem !important;
        font-family: var(--m3-font) !important;
        font-weight: 700 !important;
        transition: all 0.22s cubic-bezier(0.2, 0.8, 0.2, 1) !important;
    }

    html[data-ui-version="v1"] .btn-save:hover {
        background: var(--m3-on-primary-container) !important;
        color: var(--m3-primary-container) !important;
        box-shadow: 0 6px 20px rgba(128, 222, 197, 0.2) !important;
        transform: translateY(-2px) !important;
    }
</style>
@endpush

@section('content')
    <header class="content-header-flex">
        <div>
            <h1 style="color: var(--warna-utama, #10b981); text-transform: none;">Edit Akses User</h1>
            <p style="text-transform: none;">Atur role dan izin halaman untuk {{ $user->username }}.</p>
        </div>

        <!-- Jam & Tanggal -->
        <div class="datetime-widget">
            <div id="realtime-clock" class="time-display">00:00:00</div>
            <div id="realtime-date" class="date-display">Memuat...</div>
        </div>
    </header>

    <div class="settings-container">
        <!-- Back Button -->
        <div style="margin-top: 1.25rem; margin-bottom: 1.5rem;">
            <a href="{{ route('admin.users') }}" class="btn-back">
                <i class="fa-solid fa-chevron-left"></i> Kembali
            </a>
        </div>

        <div class="edit-layout">
            <!-- Left Panel: User Profile Details -->
            <div class="profile-sidebar-card">
                <div class="profile-avatar">
                    {{ strtoupper(substr($user->username, 0, 2)) }}
                </div>
                <h3 class="profile-username">{{ $user->username }}</h3>
                <p class="profile-email">{{ $user->email }}</p>
                <div class="profile-meta" style="width: 100%;">
                    @if($user->id === auth()->id())
                        <span class="self-account-badge">Akun Anda sendiri</span>
                    @else
                        <span class="text-muted" style="font-size: 0.75rem; display: block; margin-top: 1rem;">ID Pengguna: #{{ $user->id }}</span>
                        
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" id="form-hapus-user" style="margin-top: 1.5rem; width: 100%;">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="konfirmasiHapusUser()" style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #ef4444; width: 100%; padding: 0.65rem 1rem; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;">
                                <i class="fa-solid fa-trash-can"></i> Hapus Akun
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Right Panel: Access Settings Form -->
            <div class="form-content-card">
                <form action="{{ route('admin.users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="role" id="input-role" value="{{ $user->role }}">

                    <!-- SECTION 1: ROLE -->
                    <div class="form-section">
                        <h3 class="section-title-small">Role</h3>
                        <div class="role-cards-grid">
                            <!-- Admin Card -->
                            <div class="role-select-card {{ $user->role === 'admin' ? 'active' : '' }}" id="role-card-admin" onclick="selectRole('admin')">
                                <div class="role-icon-box">
                                    <i class="fa-solid fa-crown"></i>
                                </div>
                                <div class="role-info">
                                    <h4>Admin</h4>
                                    <p>Full access ke semua halaman</p>
                                </div>
                            </div>
                            <!-- User Card -->
                            <div class="role-select-card {{ $user->role === 'user' ? 'active' : '' }}" id="role-card-user" onclick="selectRole('user')">
                                <div class="role-icon-box">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                                <div class="role-info">
                                    <h4>User</h4>
                                    <p>Akses berdasarkan izin</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 2: AKSES HALAMAN -->
                    <div class="form-section" style="margin-top: 2rem;">
                        <h3 class="section-title-small">Akses Halaman</h3>

                        <!-- Info banner for Admin Role -->
                        <div class="admin-info-banner" id="admin-info-banner" style="display: {{ $user->role === 'admin' ? 'flex' : 'none' }};">
                            <i class="fa-solid fa-circle-check"></i>
                            <span>Admin memiliki akses penuh ke semua halaman secara otomatis.</span>
                        </div>

                        <!-- Checkboxes list for User Role -->
                        <div class="permissions-section" id="permissions-section" style="display: {{ $user->role === 'user' ? 'block' : 'none' }};">
                            <div class="permissions-list">
                                <!-- Panel Utama -->
                                <div class="perm-toggle-row">
                                    <div class="perm-info">
                                        <h4>Panel Utama</h4>
                                        <p>Pantau indikator suhu, kelembapan, intensitas cahaya, dan sakelar kontrol manual pompa secara real-time.</p>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="can_panel" value="1" {{ $user->can_panel ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>

                                <!-- Analisis -->
                                <div class="perm-toggle-row">
                                    <div class="perm-info">
                                        <h4>Analisis</h4>
                                        <p>Akses ke grafik tren sensor, data statistik harian, dan ekspor CSV/PDF.</p>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="can_analisis" value="1" {{ $user->can_analisis ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>

                                <!-- Schedules -->
                                <div class="perm-toggle-row">
                                    <div class="perm-info">
                                        <h4>Schedules</h4>
                                        <p>Akses ke konfigurasi rentang waktu penyiraman otomatis pompa air.</p>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="can_schedule" value="1" {{ $user->can_schedule ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>

                                <!-- Settings -->
                                <div class="perm-toggle-row">
                                    <div class="perm-info">
                                        <h4>Settings</h4>
                                        <p>Akses ke pengaturan target kelembapan budidaya dan pembersihan data sensor.</p>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="can_settings" value="1" {{ $user->can_settings ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>

                                <!-- 3D View -->
                                <div class="perm-toggle-row">
                                    <div class="perm-info">
                                        <h4>3D View</h4>
                                        <p>Akses ke visualisasi 3D model interaktif dari tata letak ruang budidaya.</p>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="can_view3d" value="1" {{ $user->can_view3d ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Action -->
                    <div class="form-action-footer">
                        <span class="footer-note">Perubahan berlaku segera setelah disimpan.</span>
                        <button type="submit" class="btn-save">
                            <i class="fa-solid fa-check"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/utils/clock.js') }}"></script>
    <script>
        function selectRole(role) {
            document.getElementById('input-role').value = role;

            const cardAdmin = document.getElementById('role-card-admin');
            const cardUser = document.getElementById('role-card-user');
            const permSection = document.getElementById('permissions-section');
            const adminBanner = document.getElementById('admin-info-banner');

            if (role === 'admin') {
                cardAdmin.classList.add('active');
                cardUser.classList.remove('active');
                permSection.style.display = 'none';
                adminBanner.style.display = 'flex';
            } else {
                cardUser.classList.add('active');
                cardAdmin.classList.remove('active');
                permSection.style.display = 'block';
                adminBanner.style.display = 'none';
            }
        }

        function konfirmasiHapusUser() {
            JKModal.confirm({
                type: 'danger',
                title: 'Hapus Akun Pengguna',
                message: 'Apakah Anda yakin ingin menghapus user "{{ $user->username }}" secara permanen? Tindakan ini tidak dapat dibatalkan.',
                confirmText: 'Ya, Hapus Permanen',
                cancelText: 'Batal',
                onConfirm: function () {
                    document.getElementById('form-hapus-user').submit();
                }
            });
        }
    </script>
@endpush
