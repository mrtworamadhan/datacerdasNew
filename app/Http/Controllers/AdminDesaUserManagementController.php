<?php

namespace App\Http\Controllers;

use App\Models\Desa;
use App\Models\RW;
use App\Models\RT;
use App\Models\User;
use App\Models\Posyandu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class AdminDesaUserManagementController extends Controller
{
    /**
     * Display a listing of the users (Admin RW, RT, Kader Posyandu) for the current desa.
     */
    public function index(string $subdomain)
    {
        $user = Auth::user();

        $users = User::where('desa_id', $user->desa_id)
            // ->whereIn('user_type', ['admin_rw', 'admin_rt', 'kader_posyandu'])
            ->with('rw', 'rt') // Load relasi RW dan RT
            ->get();

        return view('admin_desa.user_management.index', compact('users'));
    }

    /**
     * Show the consolidated form for generating RW/RT/Kader Posyandu accounts.
     */
    public function showGenerationForm(string $subdomain)
    {
        $user = Auth::user();

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

        $perangkatDesaRoles = Role::whereIn('name', [
            'operator_desa', 
            'bendahara_desa', 
            'admin_pelayanan', 
            'admin_kesra',
            'kepala_desa',
            'admin_umum'
        ])->get();
        $rws = Rw::where('desa_id', $user->desa_id)->get(); // Semua RW di desa
        $posyandus = Posyandu::where('desa_id', $user->desa_id)->get();
        $posyandusWithKader = User::where('user_type', 'kader_posyandu')
            ->whereNotNull('posyandu_id')
            ->pluck('posyandu_id');

        return view('admin_desa.user_management.generate', compact(
            'desa',
            'currentRwCount',
            'currentRtCount',
            'currentKaderCount',
            'rwsWithoutKader',
            'rwsWithKader',
            'rws',
            'posyandus',
            'posyandusWithKader',
            'perangkatDesaRoles'
        ));
    }

    // Di dalam class AdminDesaUserManagementController

    public function generatePerangkatDesa(Request $request, string $subdomain)
    {
        $user = Auth::user();
        $desa = $user->desa;

        $request->validate([
            'name' => 'required|string|max:255',
            'role' => [
                'required',
                'string',
                // Pastikan role yang dipilih adalah salah satu dari yang diizinkan
                Rule::in(['operator_desa', 'bendahara_desa', 'admin_pelayanan', 'admin_kesra', 'kepala_desa', 'admin_umum']),
            ],
        ]);

        $roleName = $request->role;
        $desaSlug = str_replace('-', '', Str::slug($desa->nama_desa));
        
        // Buat email otomatis: bendahara.namadesa@datacerdas.com
        $email = str_replace('_desa', '', $roleName) . ".{$desaSlug}@datacerdas.com";

        // Cek jika email sudah ada
        if (User::where('email', $email)->exists()) {
            return redirect()->back()
                ->with('error', "Akun untuk peran ini sudah ada dengan email: {$email}. Silakan edit atau hapus akun yang ada terlebih dahulu.")
                ->withInput();
        }

        $newUser = User::create([
            'name' => $request->name,
            'email' => $email,
            'password' => Hash::make('password123'),
            'desa_id' => $desa->id,
            'user_type' => $roleName, // Tetap isi user_type untuk kompatibilitas
        ]);

        $newUser->assignRole($roleName);

        $generatedAccount = [
            'tipe' => Str::title(str_replace('_', ' ', $roleName)),
            'nomor' => '-',
            'email' => $newUser->email,
            'password' => 'password123',
        ];

        return redirect()->route('admin_desa.user_management.show_generation_form', ['subdomain' => $subdomain])
                        ->with('success_perangkat', "Akun untuk {$newUser->name} berhasil dibuat!")
                        ->with('generated_accounts', [$generatedAccount]);
    }


    /**
     * Generate or update RW accounts.
     */
    public function generateRws( Request $request, string $subdomain)
    {
        $user = Auth::user();

        $desa = Desa::findOrFail($user->desa_id);

        $request->validate([
            'jumlah_rw' => 'required|integer|min:0',
        ]);

        $jumlahRw = $request->jumlah_rw;
        $generatedAccounts = [];
        $desaSlug = str_replace('-', '', Str::slug($desa->nama_desa));

        for ($i = 1; $i <= $jumlahRw; $i++) {
            $nomorRw = str_pad($i, 2, '0', STR_PAD_LEFT); // Format 01, 02

            // Cek apakah RW dengan nomor ini sudah ada untuk desa ini
            $rw = RW::firstOrCreate(
                ['desa_id' => $desa->id, 'nomor_rw' => $nomorRw],
                ['nama_ketua' => null]
            );

            // Buat atau update akun Admin RW
            $adminRwEmail = "rw{$nomorRw}.{$desaSlug}@datacerdas.com"; // Format email RW
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
    public function generateRts(Request $request, string $subdomain)
    {
        $user = Auth::user();

        $request->validate([
            'rw_id_for_rt' => 'required|exists:rws,id',
            'jumlah_rt' => 'required|integer|min:0',
        ]);

        $desa = Desa::findOrFail($user->desa_id);
        $rw = RW::where('desa_id', $user->desa_id)->findOrFail($request->rw_id_for_rt);
        $jumlahRt = $request->jumlah_rt;
        $generatedAccounts = [];
        $desaSlug = str_replace('-', '', Str::slug($desa->nama_desa));
        $nomorRwPadded = str_pad($rw->nomor_rw, 2, '0', STR_PAD_LEFT); // Untuk email RT

        for ($i = 1; $i <= $jumlahRt; $i++) {
            $nomorRt = str_pad($i, 2, '0', STR_PAD_LEFT); // Format 01, 02

            $rt = RT::firstOrCreate(
                ['desa_id' => $rw->desa_id, 'rw_id' => $rw->id, 'nomor_rt' => $nomorRt],
                ['nama_ketua' => null]
            );

            // Format email RT: [nomor_rw_padded][nomor_rt_padded]_slugdesa@tatadesa.id
            $adminRtEmail = "rt{$nomorRt}{$nomorRwPadded}.{$desaSlug}@datacerdas.com";
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
    public function generateKaders(Request $request, string $subdomain)
    {
        $user = Auth::user();

        $desa = Desa::findOrFail($user->desa_id);

        // PENYESUAIAN 1: Validasi sekarang berdasarkan posyandu_id
        $request->validate([
            'posyandu_id' => [ // Nama input di form harus 'posyandu_id'
                'required',
                'exists:posyandu,id',
                Rule::unique('users', 'posyandu_id')->where(function ($query) use ($desa) {
                    return $query->where('user_type', 'kader_posyandu')
                        ->where('desa_id', $desa->id);
                }),
            ],
        ], [
            // Pesan error juga disesuaikan
            'posyandu_id.unique' => 'Posyandu ini sudah memiliki akun Kader. Satu Posyandu hanya boleh memiliki satu akun Kader.',
        ]);

        // PENYESUAIAN 2: Kita cari data Posyandu, bukan lagi RW
        $posyandu = Posyandu::with('rws')->where('desa_id', $desa->id)->findOrFail($request->posyandu_id);

        // PENYESUAIAN 3: Membuat email dan nama user yang lebih deskriptif
        $desaSlug = str_replace('-', '', Str::slug($desa->nama_desa));
        $posyanduSlug = str_replace('-', '', Str::slug($posyandu->nama_posyandu));
        $kaderEmail = "{$posyanduSlug}.{$desaSlug}@datacerdas.com";
        DB::beginTransaction();
        try {
            $kader = User::firstOrCreate(
                ['email' => $kaderEmail],
                [
                    'name' => "Kader {$posyandu->nama_posyandu}",
                    'password' => Hash::make('password123'),
                    'user_type' => 'kader_posyandu',
                    'desa_id' => $desa->id,
                    'posyandu_id' => $posyandu->id, // <- DATA UTAMA BARU
                    'rw_id' => $posyandu->rw_id,   // <- Ini kita simpan sebagai data pendukung
                    'rt_id' => null,
                ]
            );

            // PENYESUAIAN 4: Menampilkan info yang relevan di hasil generate
            $generatedAccounts[] = [
                'tipe' => 'Kader Posyandu',
                'nomor' => "{$posyandu->nama_posyandu}-RW{$posyandu->rws->nomor_rw}",
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
    public function edit(string $subdomain,User $user)
    {
        $loggedInUser = Auth::user();
        if ($user->desa_id !== $loggedInUser->desa_id) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit pengguna ini.');
        }

        $userTypes = ['admin_rw', 'admin_rt', 'kader_posyandu'];
        $rws = RW::where('desa_id', $loggedInUser->desa_id)->get();
        $rts = RT::where('desa_id', $loggedInUser->desa_id)->get();

        return view('admin_desa.user_management.edit', compact('user', 'userTypes', 'rws', 'rts'));
    }

    public function update(Request $request, string $subdomain,User $user)
    {
        $loggedInUser = Auth::user();
        if ($user->desa_id !== $loggedInUser->desa_id) {
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

    public function destroy(string $subdomain, User $user)
    {
        $loggedInUser = Auth::user();
        if ($user->desa_id !== $loggedInUser->desa_id) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus pengguna ini.');
        }

        $user->delete();
        return redirect()->route('admin_desa.user_management.index')->with('success', 'Pengguna berhasil dihapus!');
    }
}
