<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Desa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserController extends Controller
{
    public function index(Request $request) 
    {
        $query = User::whereHas('roles', function ($query) {
            $query->where('name', '!=', 'superadmin');
        })->with('desa'); 


        if ($request->filled('desa_id')) {
            $query->where('desa_id', $request->desa_id);
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                ->orWhere('email', 'like', '%' . $searchTerm . '%');
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        $desas = Desa::orderBy('nama_desa')->get();

        return view('superadmin.users.index', compact('users', 'desas'));
    }

    public function create() 
    {
        $desas = Desa::orderBy('nama_desa')->get();
        $roles = Role::whereNotIn('name', ['superadmin'])->pluck('name', 'name');

        return view('superadmin.users.create', compact('desas', 'roles'));
    }

    public function store(Request $request) 
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name', 
            'desa_id' => 'required|exists:desas,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => $request->role, 
            'desa_id' => $request->desa_id,
        ]);

        $user->assignRole($request->role);

        return redirect()->route('admin.users.index')->with('success', 'Pengguna baru berhasil ditambahkan!');
    }

    public function edit(User $user) 
    {
        $desas = Desa::orderBy('nama_desa')->get();
        $roles = Role::whereNotIn('name', ['superadmin'])->pluck('name', 'name');

        return view('superadmin.users.edit', compact('user', 'desas', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|exists:roles,name', // <-- 4. Validasi berdasarkan role
            'desa_id' => 'required|exists:desas,id',
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'user_type' => $request->role,
            'desa_id' => $request->desa_id,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        // <-- 5. Sinkronkan role. syncRoles akan menghapus role lama dan menerapkan yang baru.
        $user->syncRoles([$request->role]);

        return redirect()->route('admin.users.index')->with('success', 'Data pengguna berhasil diperbarui!');
    }

    public function destroy(User $user) // Hapus parameter $subdomain
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dihapus!');
    }
}