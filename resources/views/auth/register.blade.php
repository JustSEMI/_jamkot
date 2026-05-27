@extends('layouts.guest')

@section('title', 'Daftar Akun')

@section('content')
    <div class="login-container">
        <div class="brand-logo">
            <div class="logo-icon">
                <i class="fa-solid fa-layer-group"></i>
            </div>
            <span>JAMKOT</span>
        </div>

        <h1 class="main-title">CREATE ACCOUNT</h1>
        <p class="sub-title">Fill in your details to get started</p>

        <!-- Notifikasi Error -->
        @if($errors->any())
            <div style="color: #ef4444; font-size: 0.875rem; margin-bottom: 1.5rem; text-align: center; background: rgba(239, 68, 68, 0.1); padding: 10px; border-radius: 6px; border: 1px solid rgba(239, 68, 68, 0.2);">
                {{ $errors->first() }}
            </div>
        @endif

        <form class="login-form" action="{{ route('register') }}" method="POST">
            @csrf

            <div class="input-group">
                <label for="username">USERNAME</label>
                <div class="input-wrapper">
                    <i class="fa-regular fa-user input-icon-left"></i>
                    <input type="text" id="username" name="username" placeholder="keju" value="{{ old('username') }}" required autofocus>
                </div>
            </div>

            <div class="input-group">
                <label for="email">EMAIL ADDRESS</label>
                <div class="input-wrapper">
                    <i class="fa-regular fa-envelope input-icon-left"></i>
                    <input type="email" id="email" name="email" placeholder="keju@chizui.dev" value="{{ old('email') }}" required>
                </div>
            </div>

            <div class="input-group">
                <label for="password">PASSWORD</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-lock input-icon-left"></i>
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                    <i class="fa-regular fa-eye input-icon-right" id="toggle-password" style="cursor: pointer;"></i>
                </div>
                <!-- Strength Indicator -->
                <div class="strength-meter" style="height: 4px; background: #262626; border-radius: 2px; margin-top: 8px; overflow: hidden;">
                    <div class="strength-bar" id="strength-bar" style="height: 100%; width: 0%; transition: all 0.3s ease;"></div>
                </div>
                <span class="strength-text" id="strength-text" style="font-size: 0.75rem; color: #6b7280; margin-top: 4px; display: block;">Enter password</span>
            </div>

            <div class="input-group">
                <label for="password_confirmation">CONFIRM PASSWORD</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-lock input-icon-left"></i>
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="••••••••" required>
                    <i class="fa-regular fa-eye input-icon-right" id="toggle-confirm-password" style="cursor: pointer;"></i>
                </div>
            </div>

            <button type="submit" class="btn-submit">Create account</button>

            <div class="divider">
                <span>or</span>
            </div>

            <p class="switch-auth">
                Already have an account? <a href="{{ route('login') }}">Sign in</a>
            </p>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        // Toggle Password Visibility
        const togglePassword = document.querySelector('#toggle-password');
        const password = document.querySelector('#password');
        togglePassword.addEventListener('click', function () {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
            this.classList.toggle('fa-eye');
        });

        const toggleConfirmPassword = document.querySelector('#toggle-confirm-password');
        const confirmPassword = document.querySelector('#password_confirmation');
        toggleConfirmPassword.addEventListener('click', function () {
            const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPassword.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
            this.classList.toggle('fa-eye');
        });

        // Password Strength Checker
        const strengthBar = document.getElementById('strength-bar');
        const strengthText = document.getElementById('strength-text');

        password.addEventListener('input', function () {
            const val = password.value;
            let score = 0;

            if (!val) {
                strengthBar.style.width = '0%';
                strengthText.textContent = 'Enter password';
                strengthText.style.color = '#6b7280';
                return;
            }

            if (val.length >= 5) score++;
            if (val.length >= 8) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            let width = '0%';
            let color = '#ef4444';
            let text = 'Weak password';

            if (score <= 2) {
                width = '25%';
                color = '#ef4444';
                text = 'Weak password';
            } else if (score === 3 || score === 4) {
                width = '60%';
                color = '#f59e0b';
                text = 'Medium password';
            } else if (score >= 5) {
                width = '100%';
                color = '#10b981';
                text = 'Strong password';
            }

            strengthBar.style.width = width;
            strengthBar.style.background = color;
            strengthText.textContent = text;
            strengthText.style.color = color;
        });
    </script>
@endpush