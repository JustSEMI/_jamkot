@extends('layouts.guest')

@section('title', 'Reset Password')

@section('content')
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

            <button type="submit" class="btn-login" style="width: 100%; padding: 0.85rem; background: var(--warna-utama, #10b981); color: #111; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s; margin-top: 1rem;">Simpan Password</button>
        </form>
    </div>
@endsection