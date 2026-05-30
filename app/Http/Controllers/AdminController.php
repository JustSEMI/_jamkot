<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserPermissionsRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Display a listing of all users and their permissions.
     */
    public function index(): View
    {
        $users = User::orderBy('username')->get();
        $totalPengguna = $users->count();
        $totalAdmin = $users->filter(fn ($u) => $u->role === 'admin')->count();
        $totalUserBiasa = $users->filter(fn ($u) => $u->role === 'user')->count();

        return view('admin.users', compact('users', 'totalPengguna', 'totalAdmin', 'totalUserBiasa'));
    }

    /**
     * Display the specified user access form.
     */
    public function edit(User $user): View
    {
        return view('admin.edit', compact('user'));
    }

    /**
     * Update the specified user role and permissions.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        if ($user->id === auth()->id() && $request->input('role') === 'user') {
            return back()->with('error', 'Anda tidak dapat menghapus akses Admin pada akun Anda sendiri.');
        }

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

    /**
     * Update specific permissions for a non-admin user.
     */
    public function updatePermissions(UpdateUserPermissionsRequest $request, User $user): RedirectResponse
    {
        if ($user->role === 'admin') {
            return back()->with('error', 'Tidak bisa mengubah permission admin.');
        }

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

    /**
     * Remove the specified user from storage.
     *
     * Rules:
     * - Admin cannot delete their own account.
     * - Admin cannot delete another admin's account.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri.');
        }

        if ($user->role === 'admin') {
            return back()->with('error', 'Sesama admin tidak bisa saling menghapus akun.');
        }

        $username = $user->username;
        $user->delete();

        return redirect()->route('admin.users')->with('sukses', "User {$username} berhasil dihapus.");
    }
}
