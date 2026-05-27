@extends('layouts.guest')

@section('title', 'Reset Password')

@section('content')
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

            <button type="submit" class="btn-login" style="width: 100%; padding: 0.85rem; background: var(--warna-utama, #10b981); color: #111; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s; margin-top: 1rem;">Cari Akun</button>
            <div style="text-align: center; margin-top: 15px;">
                <a href="{{ route('login') }}" class="forgot-link" style="font-size: 0.75rem;">Kembali ke Login</a>
            </div>
        </form>
    </div>
@endsection