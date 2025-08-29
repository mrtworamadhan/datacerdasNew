<?php

namespace App\Http\Controllers\AdminDesa;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class UserPermissionController extends Controller
{
    /**
     * Menampilkan daftar pengguna staf yang bisa diatur hak aksesnya.
     */
    public function index(string $subdomain)
    {
        $adminDesa = Auth::user();

        $staffRoles = [
            'operator_desa',
            'bendahara_desa',
            'admin_pelayanan',
            'admin_kesra',
            'admin_umum',
        ];

        $users = User::where('desa_id', $adminDesa->desa_id)
                    ->whereHas('roles', function ($query) use ($staffRoles) {
                        $query->whereIn('name', $staffRoles);
                    })
                    ->with('roles')
                    ->paginate(15);

        return view('admin_desa.permissions.index', compact('users'));
    }

    /**
     * Menampilkan form untuk mengedit hak akses seorang pengguna.
     */
    public function edit(string $subdomain, string $userId) // Terima $userId sebagai string
    {
        $user = User::findOrFail($userId); // Cari user secara manual

        $adminDesa = Auth::user();

        if ($user->desa_id !== $adminDesa->desa_id) {
            abort(403, 'Anda tidak berhak mengubah hak akses pengguna ini.');
        }

        $permissions = Permission::where('name', '!=', 'view superadmin menu')->get();
        $userPermissions = $user->permissions->pluck('name')->toArray();
        $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();

        // dd($userPermissions);

        return view('admin_desa.permissions.edit', compact('user', 'permissions', 'userPermissions'));
    }

    public function update(Request $request, string $subdomain, string $userId) // Terima $userId sebagai string
    {
        $user = User::findOrFail($userId);

        $adminDesa = Auth::user();

        if ($user->desa_id !== $adminDesa->desa_id) {
            abort(403, 'Anda tidak berhak mengubah hak akses pengguna ini.');
        }

        $validated = $request->validate([
            'permissions' => 'nullable|array'
        ]);

        $permissions = $validated['permissions'] ?? [];

        $user->syncPermissions($permissions);

        return redirect()->route('permissions.index')->with('success', 'Hak akses untuk pengguna ' . $user->name . ' berhasil diperbarui.');
    }
}