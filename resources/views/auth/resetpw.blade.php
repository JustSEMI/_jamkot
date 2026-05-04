<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | JAMKOT</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('css/mobile.css') }}">
    @vite('resources/js/app.js')
</head>

<body>
    <div class="login-container">
        <div class="login-header">
            <h1>JAMKOT</h1>
            <p>Buat password baru untuk <br> <strong style="color: #ededed;">{{ session('reset_email') }}</strong></p>
        </div>

        <form action="{{ route('password.update') }}" method="POST" class="login-form">
            @csrf
            <div class="input-group">
                <label for="password">Password Baru</label>
                <input type="password" id="password" name="password" required autofocus
                    placeholder="Minimal 8 karakter">

                @error('password')
                    <span style="color: #ef4444; font-size: 12px; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn-login">Simpan Password</button>
        </form>
    </div>
</body>

</html>