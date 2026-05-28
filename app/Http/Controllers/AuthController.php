<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show registration form.
     */
    public function register(): View
    {
        return view('auth.register');
    }

    /**
     * Store a newly registered user.
     */
    public function store(RegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        User::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'user',
            'can_panel' => true,
            'can_analisis' => true,
            'can_settings' => true,
            'can_view3d' => true,
            'can_flowchart' => true,
            'can_schedule' => false,
            'can_admin' => false,
        ]);

        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login dengan akun baru Anda.');
    }

    /**
     * Show login form.
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Handle authentication attempt.
     */
    public function authenticate(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated();
        $remember = $request->boolean('remember');

        if ($remember) {
            // Set remember cookie lifetime to 30 days (30 * 24 * 60 = 43200 minutes)
            Auth::guard('web')->setRememberDuration(43200);
        }

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $redirectTo = Auth::user()->isAdmin() ? route('panel') : route('dashboard');

            return redirect()->intended($redirectTo);
        }

        return back()->withErrors([
            'email' => 'Kredensial yang diberikan tidak cocok dengan data kami.',
        ])->onlyInput('email');
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
