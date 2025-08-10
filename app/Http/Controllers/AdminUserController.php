<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Desa; // Penting: untuk memilih desa
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Untuk hash password

class AdminUserController extends Controller
{
    public function index(string $subdomain)
    {
        // Ambil semua user kecuali super_admin, bisa difilter nanti
        $users = User::where('user_type', '!=', 'super_admin')->get();
        return view('superadmin.users.index', compact('users'));
    }

    public function create(string $subdomain)
    {
        $desas = Desa::all(); // Ambil semua desa untuk dropdown
        $userTypes = ['admin_desa', 'admin_rw', 'admin_rt', 'kader_posyandu']; // Tipe user yang bisa dibuat Super Admin
        return view('superadmin.users.create', compact('desas', 'userTypes'));
    }

    public function store(Request $request, string $subdomain)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'user_type' => 'required|in:admin_desa,admin_rw,admin_rt,kader_posyandu',
            'desa_id' => 'nullable|exists:desas,id', // desa_id opsional jika user_type tertentu
        ]);

        User::create([
            'name' => $request->name,
            'subdomain' => $request->name,
            'slug' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => $request->user_type,
            'desa_id' => $request->desa_id,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil ditambahkan!');
    }

    public function edit(string $subdomain, User $user)
    {
        if ($user->isSuperAdmin()) {
            // Super Admin tidak boleh mengedit dirinya sendiri atau sesama super admin dari sini
            return redirect()->route('admin.users.index')->with('error', 'Tidak bisa mengedit Super Admin dari halaman ini.');
        }
        $desas = Desa::all();
        $userTypes = ['admin_desa', 'admin_rw', 'admin_rt', 'kader_posyandu'];
        return view('superadmin.users.edit', compact('user', 'desas', 'userTypes'));
    }

    public function update(Request $request,string $subdomain, User $user)
    {
        if ($user->isSuperAdmin()) {
            return redirect()->route('admin.users.index')->with('error', 'Tidak bisa mengedit Super Admin.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'user_type' => 'required|in:admin_desa,admin_rw,admin_rt,kader_posyandu',
            'desa_id' => 'nullable|exists:desas,id',
        ]);

        $userData = [
            'name' => $request->name,
            'subdomain' => $request->name,
            'slug' => $request->name,
            'email' => $request->email,
            'user_type' => $request->user_type,
            'desa_id' => $request->desa_id,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        return redirect()->route('admin.users.index')->with('success', 'Data pengguna berhasil diperbarui!');
    }

    public function destroy(string $subdomain,User $user)
    {
        if ($user->isSuperAdmin()) {
            return redirect()->route('admin.users.index')->with('error', 'Tidak bisa menghapus Super Admin.');
        }
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dihapus!');
    }
}