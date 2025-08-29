<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Aset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AsetController extends Controller
{
    public function index(string $subdomain)
    {
        $user = Auth::user();
        $desa = $user->desa;

        // Ambil semua aset milik desa ini, tanpa filter RT/RW
        $asets = Aset::withoutGlobalScopes()
                     ->where('desa_id', $desa->id)
                     ->with('subSubKelompok.subKelompok.kelompok.bidang.golongan')
                     ->latest()
                     ->paginate(20);

        return view('portal.aset.index', compact('asets', 'desa'));
    }
}