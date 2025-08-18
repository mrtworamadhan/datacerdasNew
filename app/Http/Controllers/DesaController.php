<?php

namespace App\Http\Controllers;

use App\Models\Desa;
use App\Models\User;
use App\Models\RW;
use App\Models\RT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DesaController extends Controller
{
    // Definisikan opsi status langganan
    private $subscriptionStatusOptions = ['aktif', 'trial', 'nonaktif'];

    /**
     * Display a listing of the resource.
     * Accessible only by Super Admin.
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengelola desa.');
        }

        $desas = Desa::latest()->paginate(10);
        return view('superadmin.desas.index', compact('desas'));
    }

    /**
     * Show the form for creating a new resource.
     * Accessible only by Super Admin.
     */
    public function create()
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Anda tidak memiliki hak akses untuk membuat desa baru.');
        }

        $subscriptionStatusOptions = $this->subscriptionStatusOptions;
        return view('superadmin.desas.create', compact('subscriptionStatusOptions'));
    }

    /**
     * Store a newly created resource in storage.
     * Accessible only by Super Admin.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Anda tidak memiliki hak akses untuk melakukan aksi ini.');
        }

        $request->validate([
            'nama_desa' => 'required|string|max:255|unique:desas,nama_desa',
            'alamat_desa' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:100',
            'kota' => 'nullable|string|max:100',
            'provinsi' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:10',
            'nama_kades' => 'nullable|string|max:255', // Validasi nama kades
            'subscription_status' => 'required|in:aktif,trial,nonaktif',
            'subscription_ends_at' => 'nullable|date',
            'trial_ends_at' => 'nullable|date',

            // Validasi untuk akun Admin Desa yang akan digenerate
            'admin_desa_name' => 'required|string|max:255',
            'admin_desa_email' => 'required|string|email|max:255|unique:users,email', // Email harus unik di tabel users
        ]);

        DB::beginTransaction();
        try {
            $desa = Desa::create([
                'nama_desa' => $request->nama_desa,
                'subdomain' => Str::slug($request->nama_desa),
                'alamat_desa' => $request->alamat_desa,
                'kecamatan' => $request->kecamatan,
                'kota' => $request->kota,
                'provinsi' => $request->provinsi,
                'kode_pos' => $request->kode_pos,
                'nama_kades' => $request->nama_kades, // Simpan nama kades
                'subscription_status' => $request->subscription_status,
                'subscription_ends_at' => $request->subscription_ends_at,
                'trial_ends_at' => $request->trial_ends_at,
            ]);

            // Buat akun Admin Desa secara otomatis
            $adminDesaUser = User::create([
                'name' => $request->admin_desa_name,
                'email' => $request->admin_desa_email,
                'password' => Hash::make('password123'), // Password default
                'user_type' => 'admin_desa',
                'desa_id' => $desa->id, // Hubungkan dengan desa yang baru dibuat
                'rw_id' => null,
                'rt_id' => null,
                'email_verified_at' => null, // Belum diverifikasi, akan dikirim email verifikasi
            ]);

            // Kirim email verifikasi ke Admin Desa yang baru
            $adminDesaUser->sendEmailVerificationNotification();

            DB::commit();
            return redirect()->route('desas.index')->with('success', 'Desa dan akun Admin Desa berhasil ditambahkan! Email verifikasi telah dikirim ke ' . $adminDesaUser->email);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menambahkan desa: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     * Accessible only by Super Admin.
     */
    public function edit(Desa $desa)
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengedit desa.');
        }

        $subscriptionStatusOptions = $this->subscriptionStatusOptions;
        return view('superadmin.desas.edit', compact('desa', 'subscriptionStatusOptions'));
    }

    /**
     * Update the specified resource in storage.
     * Accessible only by Super Admin.
     */
    public function update(Request $request, Desa $desa)
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Anda tidak memiliki hak akses untuk memperbarui desa.');
        }

        $request->validate([
            'nama_desa' => 'required|string|max:255|unique:desas,nama_desa,'.$desa->id,
            'alamat_desa' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:100',
            'kota' => 'nullable|string|max:100',
            'provinsi' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:10',
            'nama_kades' => 'nullable|string|max:255', // Validasi nama kades
            'subscription_status' => 'required|in:aktif,trial,nonaktif',
            'subscription_ends_at' => 'nullable|date',
            'trial_ends_at' => 'nullable|date',
        ]);

        DB::beginTransaction();
        try {
            $desa->update([
                'nama_desa' => $request->nama_desa,
                'subdomain' => Str::slug($request->nama_desa),
                'alamat_desa' => $request->alamat_desa,
                'kecamatan' => $request->kecamatan,
                'kota' => $request->kota,
                'provinsi' => $request->provinsi,
                'kode_pos' => $request->kode_pos,
                'nama_kades' => $request->nama_kades, // Update nama kades
                'subscription_status' => $request->subscription_status,
                'subscription_ends_at' => $request->subscription_ends_at,
                'trial_ends_at' => $request->trial_ends_at,
            ]);

            DB::commit();
            return redirect()->route('desas.index')->with('success', 'Desa berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui desa: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     * Accessible only by Super Admin.
     */
    public function destroy(Desa $desa)
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus desa.');
        }

        DB::beginTransaction();
        try {
            // Hapus semua user yang terhubung dengan desa ini terlebih dahulu
            User::where('desa_id', $desa->id)->delete();
            // Hapus semua RW yang terhubung
            Rw::where('desa_id', $desa->id)->delete();
            // Hapus semua RT yang terhubung (akan terhapus otomatis jika onDelete('cascade') di RW)
            Rt::whereIn('rw_id', $desa->rws->pluck('id'))->delete(); // Ini opsional jika sudah cascade di RW

            $desa->delete();
            DB::commit();
            return redirect()->route('desas.index')->with('success', 'Desa dan semua data terkait berhasil dihapus!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus desa: ' . $e->getMessage());
        }
    }
}