<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    
    public function register()
    {
        return view('auth.register');
    }

    // REGISTER PAGE
    public function store(Request $request)
    {
        // VALIDASI
        $validated = $request->validate([
            'username' => 'required|unique:user,username|max:50', 
            'email'    => 'required|email|unique:user,email|max:100',
            'password' => 'required|min:5|confirmed',
        ], [
            'username.unique' => 'Username ini sudah dipakai, cari yang lain cik!',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.confirmed' => 'Konfirmasi password nggak cocok.',
            'password.min' => 'Password minimal 5 karakter.'
        ]);

        // SEND DATABASE
        $user = User::create([
            'username' => $validated['username'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // AUTO LOGIN
        return view('auth.login')->with('success', 'Registrasi berhasil! Silakan login dengan akun baru Anda.');
    }
    
    // LOGIN PAGE
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // LOGIN
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }
        return back()->withErrors([
            'email' => 'Kredensial yang diberikan tidak cocok dengan data kami.',
        ])->onlyInput('email');
    }

    // LOGOUT
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}