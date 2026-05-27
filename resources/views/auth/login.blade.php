@extends('layouts.guest')

@section('title', 'Login')

@section('content')
    <div class="login-container">
        <div class="brand-logo">
            <div class="logo-icon">
                <i class="fa-solid fa-layer-group"></i>
            </div>
            <span>JAMKOT</span>
        </div>

        <h1 class="main-title">SIGN IN</h1>
        <p class="sub-title">Enter your credentials to access your account</p>

        @if (session('status'))
            <div style="color: #10b981; font-size: 0.875rem; margin-bottom: 1.5rem; text-align: center; background: rgba(16, 185, 129, 0.1); padding: 10px; border-radius: 6px; border: 1px solid rgba(16, 185, 129, 0.2);">
                {{ session('status') }}
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST" class="login-form">
            @csrf

            <div class="input-group">
                <label for="email">EMAIL ADDRESS</label>
                <div class="input-wrapper">
                    <i class="fa-regular fa-envelope input-icon-left"></i>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required autofocus>
                </div>
                @error('email')
                    <span style="color: #ef4444; font-size: 12px; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="input-group">
                <label for="password">PASSWORD</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-lock input-icon-left"></i>
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                    <i class="fa-regular fa-eye input-icon-right" id="toggle-password" style="cursor: pointer;"></i>
                </div>
                @error('password')
                    <span style="color: #ef4444; font-size: 12px; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-actions">
                <label class="checkbox-container">
                    <input type="checkbox" name="remember" id="remember">
                    <span class="checkmark"></span>
                    Remember me for 30 days
                </label>
            </div>

            <button type="submit" class="btn-submit">Sign in</button>

            <div class="divider">
                <span>or</span>
            </div>

            <p class="switch-auth">
                Don't have an account? <a href="{{ route('register') }}">Create one</a>
            </p>
        </form>

        <div class="auth-footer">
            <i class="fa-solid fa-lock"></i>
            <span>Protected with rate limiting & encrypted sessions</span>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const togglePassword = document.querySelector('#toggle-password');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function (e) {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
            this.classList.toggle('fa-eye');
        });
    </script>
@endpush