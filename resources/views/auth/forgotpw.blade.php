<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | JAMKOT</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    @vite('resources/js/app.js')
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>JAMKOT</h1>
            <p>Jamur Automatic Monitoring & Kontrol Over Telemetry</p>
        </div>

        <form action="{{ route('password.check') }}" method="POST" class="login-form">
            @csrf
            <div class="input-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required autofocus placeholder="admin@jamkot.local">
                
                @error('email')
                    <span style="color: #ef4444; font-size: 12px; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn-login">Cari Akun</button>
            <div style="text-align: center; margin-top: 15px;">
                <a href="{{ route('login') }}" class="forgot-link" style="font-size: 0.75rem;">Kembali ke Login</a>
            </div>
        </form>
    </div>
</body>
</html>