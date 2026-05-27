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
        $users = User::orderBy('username')->get();
        $totalPengguna = $users->count();
        $totalAdmin = $users->filter(fn ($u) => $u->role === 'admin')->count();
        $totalUserBiasa = $users->filter(fn ($u) => $u->role === 'user')->count();

        return view('admin.users', compact('users', 'totalPengguna', 'totalAdmin', 'totalUserBiasa'));
    }

    /** Tampilkan form edit akses user. */
    public function edit(User $user): View
    {
        return view('admin.edit', compact('user'));
    }

    /** Update role dan permission user. */
    public function update(Request $request, User $user): RedirectResponse
    {
        if ($user->id === auth()->id() && $request->input('role') === 'user') {
            return back()->with('error', 'Anda tidak dapat menghapus akses Admin pada akun Anda sendiri.');
        }

        $validated = $request->validate([
            'role' => ['required', 'in:admin,user'],
            'can_panel' => ['nullable', 'boolean'],
            'can_analisis' => ['nullable', 'boolean'],
            'can_schedule' => ['nullable', 'boolean'],
            'can_view3d' => ['nullable', 'boolean'],
            'can_settings' => ['nullable', 'boolean'],
        ]);

        $role = $request->input('role');
        $isRoleAdmin = $role === 'admin';

        $user->update([
            'role' => $role,
            'can_panel' => $isRoleAdmin ? true : $request->boolean('can_panel'),
            'can_analisis' => $isRoleAdmin ? true : $request->boolean('can_analisis'),
            'can_schedule' => $isRoleAdmin ? true : $request->boolean('can_schedule'),
            'can_view3d' => $isRoleAdmin ? true : $request->boolean('can_view3d'),
            'can_settings' => $isRoleAdmin ? true : $request->boolean('can_settings'),
            'can_admin' => $isRoleAdmin ? true : false,
        ]);

        return redirect()->route('admin.users')->with('sukses', "Akses untuk {$user->username} berhasil diperbarui.");
    }

    /** Update permission untuk satu user. */
    public function updatePermissions(Request $request, User $user): RedirectResponse
    {
        if ($user->role === 'admin') {
            return back()->with('error', 'Tidak bisa mengubah permission admin.');
        }

        $validated = $request->validate([
            'can_panel' => ['nullable', 'boolean'],
            'can_analisis' => ['nullable', 'boolean'],
            'can_schedule' => ['nullable', 'boolean'],
            'can_view3d' => ['nullable', 'boolean'],
            'can_settings' => ['nullable', 'boolean'],
            'can_admin' => ['nullable', 'boolean'],
        ]);

        $user->update([
            'can_panel' => $request->boolean('can_panel'),
            'can_analisis' => $request->boolean('can_analisis'),
            'can_schedule' => $request->boolean('can_schedule'),
            'can_view3d' => $request->boolean('can_view3d'),
            'can_settings' => $request->boolean('can_settings'),
            'can_admin' => $request->boolean('can_admin'),
        ]);

        return back()->with('sukses', "Permission untuk {$user->username} berhasil diperbarui.");
    }

    /** Hapus user dari sistem. */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->role === 'admin' || $user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun admin atau akun sendiri.');
        }

        $username = $user->username;
        $user->delete();

        return back()->with('sukses', "User {$username} berhasil dihapus.");
    }
}
