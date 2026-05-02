<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun | JAMKOT</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
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
                <input type="text" name="username" placeholder="Masukkan username" value="{{ old('username') }}" required autofocus>
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