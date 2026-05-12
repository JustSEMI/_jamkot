<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    /** Tampilkan daftar semua user beserta permission mereka. */
    public function index(): View
    {
        $users = User::where('role', 'user')->orderBy('username')->get();

        return view('admin.users', compact('users'));
    }

    /** Update permission untuk satu user. */
    public function updatePermissions(Request $request, User $user): RedirectResponse
    {
        if ($user->isAdmin()) {
            return back()->with('error', 'Tidak bisa mengubah permission admin.');
        }

        $validated = $request->validate([
            'can_panel' => ['nullable', 'boolean'],
            'can_analisis' => ['nullable', 'boolean'],
            'can_schedule' => ['nullable', 'boolean'],
            'can_view3d' => ['nullable', 'boolean'],
            'can_settings' => ['nullable', 'boolean'],
        ]);

        $user->update([
            'can_panel' => $request->boolean('can_panel'),
            'can_analisis' => $request->boolean('can_analisis'),
            'can_schedule' => $request->boolean('can_schedule'),
            'can_view3d' => $request->boolean('can_view3d'),
            'can_settings' => $request->boolean('can_settings'),
        ]);

        return back()->with('sukses', "Permission untuk {$user->username} berhasil diperbarui.");
    }
}
