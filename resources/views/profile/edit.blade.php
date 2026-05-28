@extends('layouts.app')

@section('title', 'Edit Profil')

@push('styles')
<style>
    .profile-container {
        max-width: 600px;
        margin-top: 2rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        font-size: 0.85rem;
        font-weight: 600;
        color: #9ca3af;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .form-control {
        width: 100%;
        background: #151515;
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 8px;
        padding: 0.75rem 1rem;
        color: #ffffff;
        font-family: 'Inter', sans-serif;
        font-size: 0.95rem;
        transition: all 0.2s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--warna-utama, #10b981);
        box-shadow: 0 0 10px rgba(16, 185, 129, 0.1);
    }

    .form-control:disabled {
        background: #0d0d0d;
        color: #4b5563;
        border-color: rgba(255, 255, 255, 0.03);
        cursor: not-allowed;
    }

    .btn-submit {
        background: var(--warna-utama, #10b981);
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

    .btn-submit:hover {
        background: #059669;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
    }

    /* Material 3 Styling Overrides */
    html[data-ui-version="v1"] .form-control {
        background: var(--m3-surface-container-low) !important;
        border: 1px solid var(--m3-outline-variant) !important;
        border-radius: 12px !important;
        color: var(--m3-on-surface) !important;
        font-family: var(--m3-font) !important;
    }

    html[data-ui-version="v1"] .form-control:focus {
        border-color: var(--m3-primary) !important;
        box-shadow: none !important;
    }

    html[data-ui-version="v1"] .form-group label {
        color: var(--m3-primary) !important;
        font-family: var(--m3-font) !important;
    }

    html[data-ui-version="v1"] .btn-submit {
        background: var(--m3-primary) !important;
        color: var(--m3-on-primary) !important;
        border-radius: 100px !important;
    }

    html[data-ui-version="v1"] .btn-submit:hover {
        background: var(--m3-on-primary-container) !important;
        color: var(--m3-primary-container) !important;
    }
</style>
@endpush

@section('content')
    <header class="content-header-flex">
        <div>
            <h1 style="color: var(--warna-utama, #10b981); text-transform: none;">Edit Profil</h1>
            <p style="text-transform: none;">Perbarui informasi akun Anda.</p>
        </div>
    </header>

    <div class="settings-container">
        <div class="glow-card settings-card profile-container">
            <h2 class="section-title" style="margin: 0 0 1.5rem 0; color: #ededed; text-transform: uppercase; font-size: 0.9rem; letter-spacing: 0.05em;">Informasi Akun</h2>

            @if ($errors->any())
                <div style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); border-radius: 10px; padding: 1rem 1.25rem; margin-bottom: 1.5rem; color: #f87171; font-size: 0.875rem;">
                    <ul style="margin: 0; padding-left: 1.25rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Username -->
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" class="form-control" value="{{ old('username', $user->username) }}" required autocomplete="username">
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required autocomplete="email">
                </div>

                <!-- Password Baru -->
                <div class="form-group" style="margin-top: 2rem; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 1.5rem;">
                    <label for="password">Password Baru (Kosongkan jika tidak ingin diubah)</label>
                    <input type="password" name="password" id="password" class="form-control" autocomplete="new-password">
                </div>

                <!-- Konfirmasi Password Baru -->
                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" autocomplete="new-password">
                </div>

                <!-- Action Button -->
                <div style="margin-top: 2rem; display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn-submit">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/utils/clock.js') }}"></script>
@endpush
