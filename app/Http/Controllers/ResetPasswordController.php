<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    // TAMPILAN FORGOT PASSWORD
    public function showForgotForm()
    {
        return view('auth.forgotpw');
    }

    // CHECK EMAIL
    public function checkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            session(['reset_email' => $user->email]);
            return redirect()->route('password.reset');
        }
        return back()->withErrors(['email' => 'Email tidak terdaftar di sistem KOTAKU.'])->onlyInput('email');
    }

    // PASSOWORD RESET FORM
    public function showResetForm()
    {
        if (!session()->has('reset_email')) {
            return redirect()->route('password.request');
        }

        return view('auth.resetpw');
    }

    // UPDATE PASSWORD
    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:8'
        ]);
        $email = session('reset_email');
        $user = User::where('email', $email)->first();

        if ($user) {
            $user->update([
                'password' => Hash::make($request->password)
            ]);
            session()->forget('reset_email');

            return redirect()->route('login')->with('status', 'Password berhasil diubah! Silakan login.');
        }

        return redirect()->route('password.request')->withErrors(['email' => 'Terjadi kesalahan sistem.']);
    }
}