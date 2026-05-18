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
    <title>Login | JAMKOT</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('css/mobile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/material3.css') }}">
    @vite('resources/js/app.js')
</head>

<body>

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

    <script>
        const togglePassword = document.querySelector('#toggle-password');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function (e) {
            // toggle the type attribute
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            // toggle the eye slash icon
            this.classList.toggle('fa-eye-slash');
            this.classList.toggle('fa-eye');
        });
    </script>

</body>

</html>