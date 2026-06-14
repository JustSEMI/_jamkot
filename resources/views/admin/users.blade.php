@extends('layouts.app')

@section('title', 'Kelola User')

@push('styles')
<style>
    .stats-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
        margin-top: 1.5rem;
        margin-bottom: 2rem;
    }

    .stats-card {
        padding: 1.5rem;
        background: #111111;
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 12px;
    }

    .stats-value {
        font-size: 2rem;
        font-weight: 700;
        color: #ffffff;
        line-height: 1;
    }

    .stats-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #6b7280;
        margin-top: 0.5rem;
    }

    .users-list-wrapper {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        margin-top: 1rem;
    }

    .user-list-card {
        display: flex;
        align-items: center;
        background: #111111;
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 12px;
        padding: 1rem 1.5rem;
        gap: 1.5rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .user-list-card:hover {
        background: rgba(255, 255, 255, 0.02);
        border-color: rgba(255, 255, 255, 0.1);
        transform: translateY(-2px);
    }

    .user-avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(16, 185, 129, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.2);
        color: #10b981;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.9rem;
        text-transform: uppercase;
        flex-shrink: 0;
    }

    .user-details {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .user-name-wrapper {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .user-name {
        font-weight: 600;
        color: #ffffff;
        font-size: 1rem;
    }

    .user-self {
        color: #6b7280;
        font-size: 0.85rem;
    }

    .user-email {
        font-size: 0.85rem;
        color: #6b7280;
    }

    .badge-role-admin {
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.2);
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-role-user {
        background: rgba(156, 163, 175, 0.1);
        color: #9ca3af;
        border: 1px solid rgba(156, 163, 175, 0.2);
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .user-permissions-badges {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
        justify-content: flex-end;
        margin-right: 1.5rem;
    }

    .badge-perm-item {
        background: rgba(16, 185, 129, 0.05);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.15);
        padding: 0.25rem 0.6rem;
        border-radius: 6px;
        font-size: 0.75rem;
    }

    .badge-perm-text {
        font-size: 0.8rem;
        color: #6b7280;
    }

    .btn-edit-user {
        background: transparent;
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #ededed;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
    }

    .btn-edit-user:hover {
        background: rgba(255, 255, 255, 0.05);
        border-color: rgba(255, 255, 255, 0.2);
        color: #ffffff;
    }

    @media (max-width: 992px) {
        .user-list-card {
            flex-wrap: wrap;
            gap: 1rem;
        }
        .user-permissions-badges {
            justify-content: flex-start;
            width: 100%;
            margin-right: 0;
            order: 4;
        }
        .user-action-btn {
            order: 3;
        }
    }
    @media (max-width: 600px) {
        .stats-row {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
    }

    /* --- MATERIAL 3 EXPRESSIVE OVERRIDES --- */
    html[data-ui-version="v1"] .stats-card {
        background: var(--m3-surface-container) !important;
        border: 1px solid var(--m3-outline-variant) !important;
        border-radius: 24px !important;
        padding: 1.75rem !important;
        transition: all 0.2s ease;
    }

    html[data-ui-version="v1"] .stats-card:hover {
        background: var(--m3-surface-container-high) !important;
        transform: translateY(-2px);
    }

    html[data-ui-version="v1"] .stats-value {
        color: var(--m3-on-surface);
    }

    html[data-ui-version="v1"] .stats-card:nth-child(2) .stats-value {
        color: var(--m3-primary) !important;
    }

    html[data-ui-version="v1"] .stats-card:nth-child(3) .stats-value {
        color: var(--m3-on-surface-variant) !important;
    }

    html[data-ui-version="v1"] .stats-label {
        color: var(--m3-on-surface-variant) !important;
        font-family: var(--m3-font) !important;
        font-weight: 600 !important;
    }

    html[data-ui-version="v1"] .user-list-card {
        background: var(--m3-surface-container-low) !important;
        border: 1px solid var(--m3-outline-variant) !important;
        border-radius: 20px !important;
        padding: 1.25rem 1.75rem !important;
        transition: all 0.25s cubic-bezier(0.2, 0.8, 0.2, 1) !important;
    }

    html[data-ui-version="v1"] .user-list-card:hover {
        background: var(--m3-surface-container-high) !important;
        border-color: var(--m3-primary) !important;
        transform: translateY(-2px) !important;
    }

    html[data-ui-version="v1"] .user-avatar-circle {
        background: var(--m3-primary-container) !important;
        color: var(--m3-on-primary-container) !important;
        border: 1px solid var(--m3-primary) !important;
    }

    html[data-ui-version="v1"] .user-name {
        color: var(--m3-on-surface) !important;
    }

    html[data-ui-version="v1"] .user-email {
        color: var(--m3-on-surface-variant) !important;
    }

    html[data-ui-version="v1"] .badge-role-admin {
        background: var(--m3-primary-container) !important;
        color: var(--m3-on-primary-container) !important;
        border: 1px solid var(--m3-primary) !important;
    }

    html[data-ui-version="v1"] .badge-role-user {
        background: var(--m3-surface-container-highest) !important;
        color: var(--m3-on-surface-variant) !important;
        border: 1px solid var(--m3-outline-variant) !important;
    }

    html[data-ui-version="v1"] .badge-perm-item {
        background: var(--m3-secondary-container) !important;
        color: var(--m3-on-secondary-container) !important;
        border: 1px solid var(--m3-secondary) !important;
        border-radius: 8px !important;
        font-family: var(--m3-font) !important;
        font-weight: 500 !important;
    }

    html[data-ui-version="v1"] .badge-perm-text {
        color: var(--m3-on-surface-variant) !important;
    }

    html[data-ui-version="v1"] .btn-edit-user {
        border-radius: 100px !important;
        border: 1px solid var(--m3-outline) !important;
        background: transparent !important;
        color: var(--m3-primary) !important;
        padding: 0.6rem 1.25rem !important;
        font-family: var(--m3-font) !important;
        font-weight: 600 !important;
        transition: all 0.2s cubic-bezier(0.2, 0.8, 0.2, 1) !important;
    }

    html[data-ui-version="v1"] .btn-edit-user:hover {
        background: var(--m3-primary-container) !important;
        color: var(--m3-on-primary-container) !important;
        border-color: var(--m3-primary) !important;
    }
</style>
@endpush

@section('content')
    <header class="content-header-flex">
        <div>
            <h1 style="color: var(--warna-utama, #10b981); text-transform: none;">KELOLA USER</h1>
            <p style="text-transform: none;">Atur role dan akses halaman untuk setiap pengguna.</p>
        </div>

        <!-- Jam & Tanggal -->
        <div class="datetime-widget">
            <div id="realtime-clock" class="time-display">00:00:00</div>
            <div id="realtime-date" class="date-display">Memuat...</div>
        </div>
    </header>

    <div class="settings-container">
        <!-- Statistics Section -->
        <div class="stats-row">
            <div class="stats-card">
                <div class="stats-value">{{ $totalPengguna }}</div>
                <div class="stats-label">TOTAL PENGGUNA</div>
            </div>
            <div class="stats-card">
                <div class="stats-value" style="color: #10b981;">{{ $totalAdmin }}</div>
                <div class="stats-label">ADMIN</div>
            </div>
            <div class="stats-card">
                <div class="stats-value" style="color: #9ca3af;">{{ $totalUserBiasa }}</div>
                <div class="stats-label">USER BIASA</div>
            </div>
        </div>

        <!-- User List Section -->
        <div class="glow-card settings-card">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem;">
                <h2 class="section-title" style="margin: 0; color: #ededed; text-transform: uppercase; font-size: 0.9rem; letter-spacing: 0.05em;">Daftar Pengguna</h2>
            </div>

            <div class="users-list-wrapper">
                @forelse($users as $user)
                    <div class="user-list-card">
                        <!-- Avatar -->
                        <div class="user-avatar-circle">
                            {{ strtoupper(substr($user->username, 0, 2)) }}
                        </div>

                        <!-- Details -->
                        <div class="user-details">
                            <div class="user-name-wrapper">
                                <span class="user-name">{{ $user->username }}</span>
                                @if($user->id === auth()->id())
                                    <span class="user-self">(kamu)</span>
                                @endif
                            </div>
                            <span class="user-email">{{ $user->email }}</span>
                        </div>

                        <!-- Role -->
                        <div class="user-role">
                            @if($user->role === 'admin')
                                <span class="badge-role-admin">Admin</span>
                            @else
                                <span class="badge-role-user">User</span>
                            @endif
                        </div>

                        <!-- Permissions Badges -->
                        <div class="user-permissions-badges">
                            @if($user->role === 'admin')
                                <span class="badge-perm-item">Panel Utama</span>
                                <span class="badge-perm-item">Analisis</span>
                                <span class="badge-perm-item">Schedules</span>
                                <span class="badge-perm-item">Settings</span>
                                <span class="badge-perm-text">Full access</span>
                            @else
                                @php $hasPerm = false; @endphp
                                @if($user->can_panel)
                                    <span class="badge-perm-item">Panel Utama</span>
                                    @php $hasPerm = true; @endphp
                                @endif
                                @if($user->can_analisis)
                                    <span class="badge-perm-item">Analisis</span>
                                    @php $hasPerm = true; @endphp
                                @endif
                                @if($user->can_schedule)
                                    <span class="badge-perm-item">Schedules</span>
                                    @php $hasPerm = true; @endphp
                                @endif
                                @if($user->can_settings)
                                    <span class="badge-perm-item">Settings</span>
                                    @php $hasPerm = true; @endphp
                                @endif
                                @if($user->can_view3d)
                                    <span class="badge-perm-item">3D View</span>
                                    @php $hasPerm = true; @endphp
                                @endif
                                @if(!$hasPerm)
                                    <span class="badge-perm-text">No access</span>
                                @endif
                            @endif
                        </div>

                        <!-- Edit Button -->
                        <div class="user-action-btn">
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn-edit-user">
                                <i class="fa-solid fa-pen-to-square"></i> Edit
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="empty-state" style="text-align: center; padding: 3rem; color: #9ca3af;">
                        <span class="material-symbols-rounded" style="font-size: 3rem; opacity: 0.5; margin-bottom: 1rem;">group</span>
                        <p>Belum ada user terdaftar.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/utils/clock.js') }}"></script>
@endpush
