<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Fasum;
use App\Models\SuratSetting;
use App\Models\RW;
use App\Models\RT;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver; // Jika menggunakan Imagick
// use Intervention\Image\Drivers\Gd\Driver as GdDriver; // Jika menggunakan GD
use Illuminate\Support\Facades\Log;

class FasumController extends Controller
{
    private $jenisFasumOptions = [
        'Fasilitas Pendidikan',
        'Fasilitas Kesehatan',
        'Fasilitas Ibadah',
        'Fasilitas Olahraga',
        'Fasilitas Sanitasi & Lingkungan',
        'Fasilitas Transportasi & Ekonomi',
        'Fasilitas Umum Lainnya',
    ];

    // Definisikan kondisi di sini
    private $kondisiOptions = [
        'Baik',
        'Sedang',
        'Rusak',
    ];

    // Definisikan status kepemilikan
    private $statusKepemilikanOptions = [
        'Milik Desa',
        'Milik Swasta',
        'Milik Adat',
        'Milik Umum',
        'Lainnya',
    ];

    /**
     * Display a listing of the resource.
     */
    public function index(string $subdomain, Request $request)
    {
        $user = Auth::user();
        $desa = $user->desa;
        
        $fasumsQuery = Fasum::query();

        if ($user->hasRole('kepala_desa')) {
            $fasumsQuery->where('desa_id', $desa->id);
        }

        elseif ($user->hasRole('admin_rw')) {
            $fasumsQuery->where('rw_id', $user->rw_id);
        }

        elseif ($user->hasRole('admin_rt')) {
            $fasumsQuery->where('rt_id', $user->rt_id);
        }
 
        if ($request->filled('q')) {
            $fasumsQuery->where('nama_fasum', 'like', '%' . $request->q . '%');
        }
        if ($request->filled('status_kondisi')) {
            $fasumsQuery->where('status_kondisi', $request->status_kondisi);
        }

        $fasums = $fasumsQuery->with('photos')->latest()->paginate(10)->withQueryString();

        return view('portal.fasum.index', compact('fasums', 'desa'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $subdomain)
    {
        $user = Auth::user();
        $desa = $user->desa; // Ambil desa yang terkait dengan user
        
        $rws = collect(); // Inisialisasi koleksi kosong
        $jenisFasumOptions = $this->jenisFasumOptions;
        $kondisiOptions = $this->kondisiOptions;
        $statusKepemilikanOptions = $this->statusKepemilikanOptions;

        // Tentukan RW mana yang akan ditampilkan di dropdown
        if ($user->isAdminRw()) {
            $rws = Rw::where('id', $user->rw_id)->get();
            $rts = Rt::where('rw_id', $user->rw_id)->get();
        } elseif ($user->isAdminDesa()) {
            $rws = Rw::all(); // Admin Desa bisa memilih semua RW
            $rts = Rt::all();
        }
        
        return view('portal.fasum.create', compact(
            'rws', 'rts', 'desa', 'jenisFasumOptions', 'kondisiOptions', 'statusKepemilikanOptions'))
            ->with('subdomain', $subdomain);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'kategori' => 'required|string',
            'nama_fasum' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'status_kondisi' => 'required|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',

            'panjang' => 'nullable|string|max:255',
            'lebar' => 'nullable|string|max:255',
            'alamat_lengkap' => 'nullable|string|max:255',
            'luas_area' => 'nullable|string|max:100',
            'kapasitas' => 'nullable|string|max:100',
            'kontak_pengelola' => 'nullable|string|max:255',
            'status_kepemilikan' => 'nullable|string|max:100',

            'tinggi' => 'nullable|string|max:255',
            'luas_bangunan' => 'nullable|string|max:255',

            'rw_id' => 'required|exists:rws,id',
            'rt_id' => 'required|exists:rts,id',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $detailSpesifikasi = [
            'tinggi' => $request->tinggi ?? null,
            'luas_bangunan' => $request->luas_bangunan ?? null,
        ];
        $detailSpesifikasi = array_filter($detailSpesifikasi, function ($value) {
            return !is_null($value);
        });

        DB::beginTransaction();
        try {
            Log::info('Fasum store: Starting transaction.'); // Log awal transaksi

            $fasum = Fasum::create([
                'desa_id' => $user->desa_id,
                'rw_id' => $request->rw_id,
                'rt_id' => $request->rt_id,
                'kategori' => $request->kategori,
                'nama_fasum' => $request->nama_fasum,
                'deskripsi' => $request->deskripsi,
                'status_kondisi' => $request->status_kondisi,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'panjang' => $request->panjang,
                'lebar' => $request->lebar,
                'alamat_lengkap' => $request->alamat_lengkap,
                'luas_area' => $request->luas_area,
                'kapasitas' => $request->kapasitas,
                'kontak_pengelola' => $request->kontak_pengelola,
                'status_kepemilikan' => $request->status_kepemilikan,
            ]);
            Log::info('Fasum store: Fasum created with ID ' . $fasum->id); // Log Fasum ID

            if ($request->hasFile('photos')) {
                $manager = new ImageManager(new ImagickDriver());
                foreach ($request->file('photos') as $photo) {
                    $image = $manager->read($photo->getRealPath());
                    if ($image->width() > 800) {
                        $image->scale(width: 800);
                    }
                    $fileName = uniqid('fasum_') . '.jpg';
                    $path = 'fasum_photos/' . $fileName;
                    Storage::disk('public')->put($path, (string) $image->toJpeg());
                    $fasum->photos()->create(['path' => $path]);
                }
            }
            Log::info('Fasum store: Committing transaction.'); // Log sebelum commit
            DB::commit();
            Log::info('Fasum store: Transaction committed successfully.'); // Log setelah commit

            return redirect()->route('portal.fasum.index')->with('success', 'Fasilitas umum berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing Fasum: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Gagal menambahkan Fasilitas Umum: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Fasum $fasum)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $subdomain, Fasum $fasum)
    {
        $user = Auth::user();
        $desa = $user->desa;
        
        $jenisFasumOptions = $this->jenisFasumOptions;
        $kondisiOptions = $this->kondisiOptions;
        $statusKepemilikanOptions = $this->statusKepemilikanOptions;

        // Ambil daftar RW/RT sesuai role
        if ($user->isAdminRw()) {
            $rws = Rw::where('id', $user->rw_id)->get();
            $rts = Rt::where('rw_id', $fasum->rw_id)->get();
        } else {
            $rws = Rw::all();
            $rts = Rt::all();
        }

        // Decode detail spesifikasi jika ada
        $detailSpesifikasi = $fasum->detail_spesifikasi ? json_decode($fasum->detail_spesifikasi, true) : [];

        return view('portal.fasum.edit', compact(
            'fasum', 'rws', 'rts', 'desa',
            'jenisFasumOptions', 'kondisiOptions',
            'statusKepemilikanOptions', 'detailSpesifikasi'
        ));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(string $subdomain, Request $request, Fasum $fasum)
    {
        $request->validate([
            'kategori' => 'required|string',
            'nama_fasum' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'status_kondisi' => 'required|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',

            'panjang' => 'nullable|string|max:255',
            'lebar' => 'nullable|string|max:255',
            'alamat_lengkap' => 'nullable|string|max:255',
            'luas_area' => 'nullable|string|max:100',
            'kapasitas' => 'nullable|string|max:100',
            'kontak_pengelola' => 'nullable|string|max:255',
            'status_kepemilikan' => 'nullable|string|max:100',

            'rw_id' => 'required|exists:rws,id',
            'rt_id' => 'required|exists:rts,id',

            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        DB::beginTransaction();
        try {
            $fasum->update([
                'rw_id' => $request->rw_id,
                'rt_id' => $request->rt_id,
                'kategori' => $request->kategori,
                'nama_fasum' => $request->nama_fasum,
                'deskripsi' => $request->deskripsi,
                'status_kondisi' => $request->status_kondisi,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'panjang' => $request->panjang,
                'lebar' => $request->lebar,
                'alamat_lengkap' => $request->alamat_lengkap,
                'luas_area' => $request->luas_area,
                'kapasitas' => $request->kapasitas,
                'kontak_pengelola' => $request->kontak_pengelola,
                'status_kepemilikan' => $request->status_kepemilikan,
            ]);

            $existingPhotosToKeep = $request->existing_photos_to_keep ?? [];
            foreach ($fasum->photos as $photo) {
                if (!in_array($photo->id, $existingPhotosToKeep)) {
                    Storage::disk('public')->delete($photo->path);
                    $photo->delete();
                }
            }

            // Tambah foto baru jika ada
            if ($request->hasFile('photos')) {
                $manager = new ImageManager(new ImagickDriver());

                foreach ($request->file('photos') as $photo) {
                    $image = $manager->read($photo->getRealPath());
                    if ($image->width() > 800) {
                        $image->scale(width: 800);
                    }

                    $fileName = uniqid('fasum_') . '.jpg';
                    $path = 'fasum_photos/' . $fileName;
                    Storage::disk('public')->put($path, (string) $image->toJpeg());
                    $fasum->photos()->create(['path' => $path]);
                }
            }

            DB::commit();
            return redirect()->route('portal.fasum.index')->with('success', 'Fasilitas umum berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating Fasum: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui Fasilitas Umum: ' . $e->getMessage());
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Fasum $fasum)
    {
        //
    }

    public function updateStatus(string $subdomain, Request $request, Fasum $fasum)
    {
        $request->validate([
            'status_kondisi' => ['required', Rule::in(['Baik', 'Sedang', 'Rusak'])],
        ]);

        $fasum->status_kondisi = $request->status_kondisi;
        $fasum->save();

        return back()->with('success', 'Status kondisi berhasil diperbarui.');
    }

}
