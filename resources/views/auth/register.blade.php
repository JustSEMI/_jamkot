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
    <title>Daftar Akun | JAMKOT</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('css/mobile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/material3.css') }}">
    @vite('resources/js/app.js')
</head>

<body>

    <div class="login-container">
        <div class="login-header">
            <h1>JAMKOT</h1>
            <p>Registrasi Akses Panel</p>
        </div>

        <!-- Notifikasi Error -->
        @if($errors->any())
            <div class="alert-error">
                {{ $errors->first() }}
            </div>
        @endif

        <form class="login-form" action="{{ route('register') }}" method="POST">
            @csrf

            <div class="input-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Masukkan username" value="{{ old('username') }}"
                    required autofocus>
            </div>

            <div class="input-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="contoh@jamkot.local" value="{{ old('email') }}" required>
            </div>

            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Minimal 5 karakter" required>
            </div>

            <div class="input-group">
                <label>Konfirmasi Password</label>
                <input type="password" name="password_confirmation" placeholder="Ulangi password" required>
            </div>

            <button type="submit" class="btn-login">Daftar Akun</button>
        </form>

        <div class="login-footer">
            <p>Sudah punya akun? <a href="{{ route('login') }}" class="forgot-link">Masuk</a></p>
        </div>
    </div>

</body>

</html>