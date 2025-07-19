<?php

namespace App\Http\Controllers;

use App\Models\Desa;
use App\Models\RW;
use App\Models\RT;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminDesaUserManagementController extends Controller
{
    /**
     * Display a listing of the users (Admin RW, RT, Kader Posyandu) for the current desa.
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user->isAdminDesa() && !$user->isSuperAdmin() || !$user->desa_id) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $users = User::where('desa_id', $user->desa_id)
            ->whereIn('user_type', ['admin_rw', 'admin_rt', 'kader_posyandu'])
            ->with('rw', 'rt') // Load relasi RW dan RT
            ->get();

        return view('admin_desa.user_management.index', compact('users'));
    }

    /**
     * Show the consolidated form for generating RW/RT/Kader Posyandu accounts.
     */
    public function showGenerationForm()
    {
        $user = Auth::user();
        if (!$user->isAdminDesa() && !$user->isSuperAdmin() || !$user->desa_id) {
            abort(403, 'Anda tidak memiliki akses untuk melakukan aksi ini.');
        }

        $desa = Desa::findOrFail($user->desa_id);

        // Hitung jumlah RW, RT, Kader yang sudah ada
        $currentRwCount = Rw::where('desa_id', $user->desa_id)->count();
        $currentRtCount = Rt::where('desa_id', $user->desa_id)->count();
        $currentKaderCount = User::where('desa_id', $user->desa_id)
            ->where('user_type', 'kader_posyandu')
            ->count();

        // Ambil daftar RW yang belum memiliki Kader Posyandu
        $rwsWithoutKader = Rw::where('desa_id', $user->desa_id)
            ->whereDoesntHave('users', function ($query) {
                $query->where('user_type', 'kader_posyandu');
            })
            ->get();

        // Ambil daftar RW yang sudah memiliki Kader Posyandu
        $rwsWithKader = Rw::where('desa_id', $user->desa_id)
            ->whereHas('users', function ($query) {
                $query->where('user_type', 'kader_posyandu');
            })
            ->get();

        $rws = Rw::where('desa_id', $user->desa_id)->get(); // Semua RW di desa

        return view('admin_desa.user_management.generate', compact(
            'desa',
            'currentRwCount',
            'currentRtCount',
            'currentKaderCount',
            'rwsWithoutKader',
            'rwsWithKader',
            'rws' // Teruskan semua RW untuk dropdown RT
        ));
    }

    /**
     * Generate or update RW accounts.
     */
    public function generateRws(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdminDesa() && !$user->isSuperAdmin() || !$user->desa_id) {
            abort(403, 'Anda tidak memiliki akses untuk melakukan aksi ini.');
        }

        $desa = Desa::findOrFail($user->desa_id);

        $request->validate([
            'jumlah_rw' => 'required|integer|min:0',
        ]);

        $jumlahRw = $request->jumlah_rw;
        $generatedAccounts = [];
        $desaSlug = Str::slug($desa->nama_desa);

        for ($i = 1; $i <= $jumlahRw; $i++) {
            $nomorRw = str_pad($i, 2, '0', STR_PAD_LEFT); // Format 01, 02

            // Cek apakah RW dengan nomor ini sudah ada untuk desa ini
            $rw = RW::firstOrCreate(
                ['desa_id' => $desa->id, 'nomor_rw' => $nomorRw],
                ['nama_ketua' => null]
            );

            // Buat atau update akun Admin RW
            $adminRwEmail = "rw{$nomorRw}_{$desaSlug}@tatadesa.id"; // Format email RW
            $adminRw = User::firstOrCreate(
                ['email' => $adminRwEmail],
                [
                    'name' => "Admin RW {$nomorRw} {$desa->nama_desa}",
                    'password' => Hash::make('password123'),
                    'user_type' => 'admin_rw',
                    'desa_id' => $desa->id,
                    'rw_id' => $rw->id,
                    'rt_id' => null,
                ]
            );
            $generatedAccounts[] = [
                'tipe' => 'Admin RW',
                'nomor' => $nomorRw,
                'email' => $adminRw->email,
                'password' => 'password123',
            ];
        }

        return redirect()->route('admin_desa.user_management.show_generation_form')->with([
            'success_rw' => 'Akun RW berhasil digenerate/diperbarui!',
            'generated_accounts' => $generatedAccounts,
        ]);
    }

    /**
     * Generate or update RT accounts for a specific RW.
     */
    public function generateRts(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdminDesa() && !$user->isSuperAdmin() || !$user->desa_id) {
            abort(403, 'Anda tidak memiliki akses untuk melakukan aksi ini.');
        }

        $request->validate([
            'rw_id_for_rt' => 'required|exists:rws,id',
            'jumlah_rt' => 'required|integer|min:0',
        ]);

        $rw = RW::where('desa_id', $user->desa_id)->findOrFail($request->rw_id_for_rt);
        $jumlahRt = $request->jumlah_rt;
        $generatedAccounts = [];
        $desaSlug = Str::slug($rw->desa->nama_desa);
        $nomorRwPadded = str_pad($rw->nomor_rw, 2, '0', STR_PAD_LEFT); // Untuk email RT

        for ($i = 1; $i <= $jumlahRt; $i++) {
            $nomorRt = str_pad($i, 2, '0', STR_PAD_LEFT); // Format 01, 02

            $rt = RT::firstOrCreate(
                ['desa_id' => $rw->desa_id, 'rw_id' => $rw->id, 'nomor_rt' => $nomorRt],
                ['nama_ketua' => null]
            );

            // Format email RT: [nomor_rw_padded][nomor_rt_padded]_slugdesa@tatadesa.id
            $adminRtEmail = "{$nomorRwPadded}{$nomorRt}_{$desaSlug}@tatadesa.id";
            $adminRt = User::firstOrCreate(
                ['email' => $adminRtEmail],
                [
                    'name' => "Admin RT {$nomorRt} RW {$nomorRwPadded} {$rw->desa->nama_desa}",
                    'password' => Hash::make('password123'),
                    'user_type' => 'admin_rt',
                    'desa_id' => $rw->desa_id,
                    'rw_id' => $rw->id,
                    'rt_id' => $rt->id,
                ]
            );
            $generatedAccounts[] = [
                'tipe' => 'Admin RT',
                'nomor' => "RW{$nomorRwPadded} RT{$nomorRt}",
                'email' => $adminRt->email,
                'password' => 'password123',
            ];
        }

        return redirect()->route('admin_desa.user_management.show_generation_form')->with([
            'success_rt' => 'Akun RT berhasil digenerate/diperbarui untuk RW ' . $rw->nomor_rw . '!',
            'generated_accounts' => $generatedAccounts,
        ]);
    }

    /**
     * Generate or update Kader Posyandu accounts.
     */
    public function generateKaders(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdminDesa() && !$user->isSuperAdmin() || !$user->desa_id) {
            abort(403, 'Anda tidak memiliki akses untuk melakukan aksi ini.');
        }

        $desa = Desa::findOrFail($user->desa_id);

        $request->validate([
            'rw_id_for_kader' => [
                'required',
                'exists:rws,id',
                Rule::unique('users', 'rw_id')->where(function ($query) use ($desa) {
                    return $query->where('user_type', 'kader_posyandu')
                        ->where('desa_id', $desa->id);
                }),
            ],
        ], [
            'rw_id.unique' => 'RW ini sudah memiliki akun Kader Posyandu. Satu RW hanya boleh memiliki satu akun Kader Posyandu.',
        ]);

        $rw = Rw::where('desa_id', $desa->id)->findOrFail($request->rw_id_for_kader);
        $generatedAccounts = [];
        $kaderEmail = "kader_rw{$rw->nomor_rw}_{$desa->slug}@tatadesa.id"; // Email lebih spesifik

        DB::beginTransaction();
        try {
            $kader = User::firstOrCreate(
                ['email' => $kaderEmail],
                [
                    'name' => "Kader Posyandu RW {$rw->nomor_rw} {$desa->nama_desa}",
                    'password' => Hash::make('password123'),
                    'user_type' => 'kader_posyandu',
                    'desa_id' => $desa->id,
                    'rw_id' => $rw->id, // Isi rw_id
                    'rt_id' => null, // Kader tidak punya RT spesifik
                ]
            );
            $generatedAccounts[] = [
                'tipe' => 'Kader Posyandu',
                'nomor' => $rw->nomor_rw, // Nomor RW
                'email' => $kader->email,
                'password' => 'password123',
            ];

            DB::commit();
            return redirect()->route('admin_desa.user_management.show_generation_form')->with([
                'success_kader' => 'Akun Kader Posyandu berhasil digenerate/diperbarui!',
                'generated_accounts' => $generatedAccounts,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error generating Kaders: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal generate akun Kader Posyandu: ' . $e->getMessage())->withInput();
        }
    }

    // Metode edit, update, destroy tetap sama seperti sebelumnya
    public function edit(User $user)
    {
        $loggedInUser = Auth::user();
        if ($user->desa_id !== $loggedInUser->desa_id || $user->isSuperAdmin()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit pengguna ini.');
        }

        $userTypes = ['admin_rw', 'admin_rt', 'kader_posyandu'];
        $rws = RW::where('desa_id', $loggedInUser->desa_id)->get();
        $rts = RT::where('desa_id', $loggedInUser->desa_id)->get();

        return view('admin_desa.user_management.edit', compact('user', 'userTypes', 'rws', 'rts'));
    }

    public function update(Request $request, User $user)
    {
        $loggedInUser = Auth::user();
        if ($user->desa_id !== $loggedInUser->desa_id || $user->isSuperAdmin()) {
            abort(403, 'Anda tidak memiliki akses untuk memperbarui pengguna ini.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'user_type' => 'required|in:admin_rw,admin_rt,kader_posyandu',
            'rw_id' => 'nullable|exists:rws,id',
            'rt_id' => 'nullable|exists:rts,id',
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'user_type' => $request->user_type,
            'rw_id' => $request->rw_id,
            'rt_id' => $request->rt_id,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        return redirect()->route('admin_desa.user_management.index')->with('success', 'Data pengguna berhasil diperbarui!');
    }

    public function destroy(User $user)
    {
        $loggedInUser = Auth::user();
        if ($user->desa_id !== $loggedInUser->desa_id || $user->isSuperAdmin()) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus pengguna ini.');
        }

        $user->delete();
        return redirect()->route('admin_desa.user_management.index')->with('success', 'Pengguna berhasil dihapus!');
    }
}
