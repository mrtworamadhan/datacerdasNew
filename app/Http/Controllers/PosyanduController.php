<?php

namespace App\Http\Controllers;

use App\Models\Posyandu;
use App\Models\RW;
use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosyanduController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $subdomain)
    {
        $posyandu = Posyandu::with('rws')->latest()->get();

        return view('admin_desa.posyandu.index', compact('posyandu'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $subdomain)
    {
        // Kita ambil semua data RW untuk ditampilkan di dropdown pilihan
        $rws = RW::all();
        return view('admin_desa.posyandu.create', compact('rws'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $subdomain)
    {
        // dd($request->all());
        $validated = $request->validate([
            'desa_id' => 'required|string',
            'nama_posyandu' => 'required|string|max:100',
            'rw_id' => 'required|exists:rws,id',
            'alamat' => 'required|string',
        ]);


        Posyandu::create($validated);

        return redirect('posyandu')->with('status', 'Data Posyandu berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $subdomain, string $id)
    {
        //
    }
    public function getDetail(string $subdomain, Posyandu $posyandu)
    {
        // Load relasi 'rws' dan 'kaders'
        $posyandu->load(['rws', 'kaders']);
        return response()->json($posyandu);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $subdomain, string $id)
    {
        $posyandu = Posyandu::findOrFail($id);
        $rws = RW::all();
        return view('admin_desa.posyandu.edit', compact('posyandu', 'rws'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,string $subdomain, string $id)
    {
        $validated = $request->validate([
            'nama_posyandu' => 'required|string|max:100',
            'rw_id' => 'required|exists:rws,id',
            'alamat' => 'required|string',
        ]);

        $posyandu = Posyandu::findOrFail($id);
        $posyandu->update($validated);

        return redirect('posyandu')->with('status', 'Data Posyandu berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $subdomain, string $id)
    {
        $posyandu = Posyandu::findOrFail($id);
        $posyandu->delete();

        return redirect('posyandu')->with('status', 'Data Posyandu berhasil dihapus!');
    }

    public function kaderManager(string $subdomain, Posyandu $posyandu)
    {
        // Load relasi 'kaders' yang sudah kita buat di Model Posyandu
        $posyandu->load('kaders');
        return view('admin_desa.posyandu.kaders', compact('posyandu'));
    }

    public function storeKader(Request $request,string $subdomain, Posyandu $posyandu)
    {
        // 1. Validasi: pastikan warga_id dikirim dan ada di tabel wargas
        $request->validate([
            'warga_id' => 'required|exists:wargas,id'
        ]);

        // 2. Cek apakah kader sudah terdaftar sebelumnya di posyandu ini
        $isExist = $posyandu->kaders()->where('warga_id', $request->warga_id)->exists();

        if ($isExist) {
            return back()->with('error', 'Warga ini sudah terdaftar sebagai kader di Posyandu ini!');
        }

        // 3. Jika belum, tambahkan relasinya (attach)
        $posyandu->kaders()->attach($request->warga_id);

        return back()->with('status', 'Kader berhasil ditambahkan!');
    }

    public function destroyKader(string $subdomain, $kader_pivot_id)
    {
        DB::table('kader_posyandu')->where('id', $kader_pivot_id)->delete();

        return back()->with('status', 'Kader berhasil dihapus.');
    }
}
