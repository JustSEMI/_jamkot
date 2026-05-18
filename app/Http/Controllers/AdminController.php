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
            'can_flowchart' => ['nullable', 'boolean'],
            'can_settings' => ['nullable', 'boolean'],
            'can_admin' => ['nullable', 'boolean'],
            'can_prediction' => ['nullable', 'boolean'],
        ]);

        $user->update([
            'can_panel' => $request->boolean('can_panel'),
            'can_analisis' => $request->boolean('can_analisis'),
            'can_schedule' => $request->boolean('can_schedule'),
            'can_view3d' => $request->boolean('can_view3d'),
            'can_flowchart' => $request->boolean('can_flowchart'),
            'can_settings' => $request->boolean('can_settings'),
            'can_admin' => $request->boolean('can_admin'),
            'can_prediction' => $request->boolean('can_prediction'),
        ]);

        return back()->with('sukses', "Permission untuk {$user->username} berhasil diperbarui.");
    }

    /** Hapus user dari sistem. */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->isAdmin() || $user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun admin atau akun sendiri.');
        }

        $username = $user->username;
        $user->delete();

        return back()->with('sukses', "User {$username} berhasil dihapus.");
    }
}
