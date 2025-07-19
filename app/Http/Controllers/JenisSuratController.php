<?php

namespace App\Http\Controllers;

use App\Models\JenisSurat;
use App\Models\Desa;
use App\Models\KlasifikasiSurat;
use App\Models\SuratSetting;
use Illuminate\Http\Request;

class JenisSuratController extends Controller
{
    public function index()
    {
        $jenisSurats = JenisSurat::with('klasifikasi')->latest()->paginate(10);
        return view('admin_desa.jenis_surat.index', compact('jenisSurats'));
    }

    public function create()
    {
        $desa = Desa::findOrFail(auth()->user()->desa_id);
        $klasifikasiSurats = KlasifikasiSurat::orderBy('kode')->get();
        $suratSetting = SuratSetting::firstOrCreate(['desa_id' => auth()->user()->desa_id]);
        return view('admin_desa.jenis_surat.create', compact('klasifikasiSurats', 'suratSetting', 'desa'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_surat' => 'required|string|max:255',
            'judul_surat' => 'required|string|max:255',
            'klasifikasi_surat_id' => 'required|exists:klasifikasi_surats,id',
            'isi_template' => 'required|string',
            'persyaratan_text' => 'nullable|string',
            'custom_fields_text' => 'nullable|string',
        ]);

        if (!empty($validated['persyaratan_text'])) {
            $validated['persyaratan'] = array_filter(array_map('trim', explode("\n", $validated['persyaratan_text'])));
        }
        if (!empty($validated['custom_fields_text'])) {
            $validated['custom_fields'] = array_filter(array_map('trim', explode("\n", $validated['custom_fields_text'])));
        }

        JenisSurat::create($validated);

        return redirect()->route('jenis-surat.index')->with('success', 'Jenis surat berhasil ditambahkan.');
    }

    public function show(JenisSurat $jenisSurat)
    {
        $suratSetting = SuratSetting::firstOrCreate(['desa_id' => auth()->user()->desa_id]);
        return view('admin_desa.jenis_surat.show', compact('jenisSurat', 'suratSetting'));
    }

    public function edit(JenisSurat $jenisSurat)
    {
        $desa = Desa::findOrFail(auth()->user()->desa_id);
        $klasifikasiSurats = KlasifikasiSurat::orderBy('kode')->get();
        $suratSetting = SuratSetting::firstOrCreate(['desa_id' => auth()->user()->desa_id]);
        return view('admin_desa.jenis_surat.edit', compact('jenisSurat', 'klasifikasiSurats', 'suratSetting', 'desa'));
    }

    public function update(Request $request, JenisSurat $jenisSurat)
    {
        $validated = $request->validate([
            'nama_surat' => 'required|string|max:255',
            'judul_surat' => 'required|string|max:255',
            'klasifikasi_surat_id' => 'required|exists:klasifikasi_surats,id',
            'isi_template' => 'required|string',
            'persyaratan_text' => 'nullable|string',
            'custom_fields_text' => 'nullable|string',
        ]);

        if (!empty($validated['persyaratan_text'])) {
            $validated['persyaratan'] = array_filter(array_map('trim', explode("\n", $validated['persyaratan_text'])));
        } else {
            $validated['persyaratan'] = null;
        }
        if (!empty($validated['custom_fields_text'])) {
            $validated['custom_fields'] = array_filter(array_map('trim', explode("\n", $validated['custom_fields_text'])));
        } else {
            $validated['custom_fields'] = null;
        }

        $jenisSurat->update($validated);

        return redirect()->route('jenis-surat.index')->with('success', 'Jenis surat berhasil diperbarui.');
    }

    public function destroy(JenisSurat $jenisSurat)
    {
        $jenisSurat->delete();
        return redirect()->route('jenis-surat.index')->with('success', 'Jenis surat berhasil dihapus.');
    }
}