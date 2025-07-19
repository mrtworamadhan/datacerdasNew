<?php

namespace App\Http\Controllers;

use App\Models\Fasum;
use App\Models\FasumPhoto;
use App\Models\Desa;
use App\Models\Rw;
use App\Models\Rt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver; // Jika menggunakan Imagick
// use Intervention\Image\Drivers\Gd\Driver as GdDriver; // Jika menggunakan GD
use Illuminate\Support\Facades\Log; // Pastikan ini di-import

class FasumController extends Controller
{
    // Definisikan kategori di sini agar mudah dikelola
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
     * Display a listing of Fasum for admin users (Admin Desa, RW, RT).
     * This will be filtered by Global Scope.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdminDesa() && !$user->isSuperAdmin() && !$user->isAdminRw() && !$user->isAdminRt()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengelola fasilitas umum.');
        }

        $query = Fasum::with('desa', 'rw', 'rt', 'photos');

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nama_fasum', 'like', '%' . $searchTerm . '%')
                    ->orWhere('kategori', 'like', '%' . $searchTerm . '%')
                    ->orWhere('alamat_lengkap', 'like', '%' . $searchTerm . '%');
            });
        }
        if ($request->filled('kondisi')) {
            $query->where('status_kondisi', $request->kondisi);
        }
        if ($request->filled('status_kepemilikan')) {
            $query->where('status_kepemilikan', $request->status_kepemilikan);
        }
        if ($request->filled('jenis_fasum')) {
            $query->where('kategori', $request->jenis_fasum);
        }
        if ($user->isAdminDesa() || $user->isSuperAdmin()) {
            if ($request->filled('rw_id')) {
                $query->where('rw_id', $request->rw_id);
            }
            if ($request->filled('rt_id')) {
                $query->where('rt_id', $request->rt_id);
            }
        }

        $fasums = $query->latest()->paginate(10);

        $totalFasum = Fasum::count();
        $fasumRusak = Fasum::where('status_kondisi', 'Rusak')->count();

        $fasumPerKategori = Fasum::select('kategori as jenis_fasum', DB::raw('count(*) as total'))
            ->groupBy('kategori')
            ->get();

        $rwWithoutSpecificFasum = [];
        if ($user->isAdminDesa() || $user->isSuperAdmin()) {
            $allRwsInDesa = Rw::where('desa_id', $user->desa_id)->with('fasums')->get();

            $fasumTypesToCheck = [
                'Fasilitas Kesehatan' => ['Posyandu', 'Puskesmas Pembantu'],
                'Fasilitas Pendidikan' => ['Sekolah Dasar', 'SMP', 'SMA'],
                'Fasilitas Sanitasi & Lingkungan' => ['MCK Umum', 'Taman'],
                'Fasilitas Olahraga' => ['Lapangan Sepak Bola', 'Lapangan Bulutangkis', 'Lapangan Voli'],
                'Fasilitas Ibadah' => ['Masjid', 'Gereja', 'Pura', 'Vihara', 'Klenteng'],
            ];

            foreach ($fasumTypesToCheck as $categoryName => $fasumJenisList) {
                $rwList = [];
                foreach ($allRwsInDesa as $rw) {
                    $hasFasum = false;
                    foreach ($fasumJenisList as $fasumJenis) {
                        if ($rw->fasums->where('kategori', $fasumJenis)->isNotEmpty()) {
                            $hasFasum = true;
                            break;
                        }
                    }
                    if (!$hasFasum) {
                        $rwList[] = 'RW ' . $rw->nomor_rw;
                    }
                }
                if (!empty($rwList)) {
                    $rwWithoutSpecificFasum[$categoryName] = $rwList;
                }
            }
        }

        $rws = Rw::all();
        $rts = Rt::all();

        $jenisFasumOptions = $this->jenisFasumOptions;
        $kondisiOptions = $this->kondisiOptions;
        $statusKepemilikanOptions = $this->statusKepemilikanOptions;

        return view('admin_desa.fasum.index', compact(
            'fasums',
            'totalFasum',
            'fasumRusak',
            'fasumPerKategori',
            'rwWithoutSpecificFasum',
            'rws',
            'rts',
            'jenisFasumOptions',
            'kondisiOptions',
            'statusKepemilikanOptions'
        ));
    }

    public function create()
    {
        $user = Auth::user();
        if (!$user->isAdminDesa() && !$user->isSuperAdmin() && !$user->isAdminRw() && !$user->isAdminRt()) {
            abort(403, 'Anda tidak memiliki hak akses untuk menambah fasilitas umum.');
        }

        $rws = Rw::all();
        $rts = Rt::all();

        return view('admin_desa.fasum.create', [
            'jenisFasumOptions' => $this->jenisFasumOptions,
            'kondisiOptions' => $this->kondisiOptions,
            'statusKepemilikanOptions' => $this->statusKepemilikanOptions,
            'rws' => $rws,
            'rts' => $rts,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdminDesa() && !$user->isSuperAdmin() && !$user->isAdminRw() && !$user->isAdminRt()) {
            abort(403, 'Anda tidak memiliki hak akses untuk melakukan aksi ini.');
        }

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
                'detail_spesifikasi' => !empty($detailSpesifikasi) ? json_encode($detailSpesifikasi) : null,
            ]);
            Log::info('Fasum store: Fasum created with ID ' . $fasum->id); // Log Fasum ID

            if ($request->hasFile('photos')) {
                // Inisialisasi ImageManager dengan driver yang Anda inginkan
                $manager = new ImageManager(new ImagickDriver()); // Atau new GdDriver()

                foreach ($request->file('photos') as $photo) {
                    Log::info('Fasum store: Processing photo ' . $photo->getClientOriginalName()); // Log nama foto
                    $image = $manager->read($photo->getRealPath());

                    $originalWidth = $image->width();

                    if ($image->width() > 800) {
                        $image->scale(height: 300);
                        
                    }

                    $fileName = uniqid('fasum_') . '.jpg'; // Simpan semua sebagai jpg
                    $path = 'fasum_photos/' . $fileName;

                    Storage::disk('public')->put($path, (string) $image); // Cast ke string sebelum simpan // Encode dengan kualitas
                    Log::info('Fasum store: Photo saved to storage: ' . $path); // Log path penyimpanan

                    $fasum->photos()->create(['path' => $path]);
                    Log::info('Fasum store: Photo record created in DB for ' . $fileName); // Log record foto
                }
            }
            Log::info('Fasum store: Committing transaction.'); // Log sebelum commit
            DB::commit();
            Log::info('Fasum store: Transaction committed successfully.'); // Log setelah commit

            return redirect()->route('fasum.index')->with('success', 'Fasilitas umum berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing Fasum: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            // dd($e->getMessage()); // Tetap aktifkan dd() untuk debugging langsung
            return redirect()->back()->with('error', 'Gagal menambahkan Fasilitas Umum: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Fasum $fasum)
    {
        $user = Auth::user();
        if (!$user->isAdminDesa() && !$user->isSuperAdmin() && !$user->isAdminRw() && !$user->isAdminRt()) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat fasilitas umum.');
        }

        $fasum->load('desa', 'rw', 'rt', 'photos');

        return view('admin_desa.fasum.show', compact('fasum'));
    }

    public function edit(Fasum $fasum)
    {
        $user = Auth::user();
        if (!$user->isAdminDesa() && !$user->isSuperAdmin() && !$user->isAdminRw() && !$user->isAdminRt()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengedit fasilitas umum.');
        }

        $rws = Rw::all();
        $rts = Rt::all();

        return view('admin_desa.fasum.edit', [
            'fasum' => $fasum,
            'jenisFasumOptions' => $this->jenisFasumOptions,
            'kondisiOptions' => $this->kondisiOptions,
            'statusKepemilikanOptions' => $this->statusKepemilikanOptions,
            'rws' => $rws,
            'rts' => $rts,
        ]);
    }

    public function update(Request $request, Fasum $fasum)
    {
        $user = Auth::user();
        if (!$user->isAdminDesa() && !$user->isSuperAdmin() && !$user->isAdminRw() && !$user->isAdminRt()) {
            abort(403, 'Anda tidak memiliki hak akses untuk memperbarui fasilitas umum.');
        }

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
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'existing_photos_to_keep' => 'nullable|array',
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
            $fasum->update([
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
                'detail_spesifikasi' => !empty($detailSpesifikasi) ? json_encode($detailSpesifikasi) : null,
                'rw_id' => $request->rw_id,
                'rt_id' => $request->rt_id,
            ]);

            $existingPhotosToKeep = $request->existing_photos_to_keep ?? [];
            foreach ($fasum->photos as $photo) {
                if (!in_array($photo->id, $existingPhotosToKeep)) {
                    Storage::disk('public')->delete($photo->path);
                    $photo->delete();
                }
            }

            if ($request->hasFile('photos')) {
                $manager = new ImageManager(new ImagickDriver());

                foreach ($request->file('photos') as $photo) {
                    Log::info('Fasum store: Processing photo ' . $photo->getClientOriginalName());

                    $image = $manager->read($photo->getRealPath());

                    if ($image->width() > 800) {
                        $image->scale(height: 300);
                    }

                    $jpegImage = $image->toJpeg(80); // compress to JPEG 80% quality

                    $fileName = uniqid('fasum_') . '.jpg';
                    $path = 'fasum_photos/' . $fileName;

                    Storage::disk('public')->put($path, (string) $jpegImage);

                    $fasum->photos()->create(['path' => $path]);
                    Log::info('Fasum store: Photo saved and DB record created for ' . $fileName);
                }
            }
            Log::info('Fasum store: Committing transaction.'); // Log sebelum commit
            DB::commit();
            Log::info('Fasum store: Transaction committed successfully.'); // Log setelah commit

            return redirect()->route('fasum.index')->with('success', 'Fasilitas umum berhasil diperbarui.');
        } catch (\Exception | \Throwable $e) {
            DB::rollBack();
            Log::error('Error updating Fasum: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Gagal memperbarui Fasilitas Umum: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Fasum $fasum)
    {
        $user = Auth::user();
        if (!$user->isAdminDesa() && !$user->isSuperAdmin() && !$user->isAdminRw() && !$user->isAdminRt()) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus fasilitas umum.');
        }

        DB::beginTransaction();
        try {
            foreach ($fasum->photos as $photo) {
                Storage::disk('public')->delete($photo->path);
            }
            $fasum->delete();
            return redirect()->route('fasum.index')->with('success', 'Fasilitas umum berhasil dihapus.');
        } catch (\Exception | \Throwable $e) {
            DB::rollBack();
            Log::error('Error deleting Fasum: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Gagal menghapus Fasilitas Umum: ' . $e->getMessage());
        }
    }

    public function destroyPhoto(FasumPhoto $photo)
    {
        $user = Auth::user();
        if (!$user->isAdminDesa() && !$user->isSuperAdmin() && !$user->isAdminRw() && !$user->isAdminRt()) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus foto fasilitas umum.');
        }

        if ($photo->fasum->desa_id !== $user->desa_id) {
            abort(403, 'Foto ini bukan milik desa Anda.');
        }

        DB::beginTransaction();
        try {
            Storage::disk('public')->delete($photo->path);
            $photo->delete();
            DB::commit();
            return back()->with('success', 'Foto berhasil dihapus.');
        } catch (\Exception | \Throwable $e) {
            DB::rollBack();
            Log::error('Error deleting Fasum photo: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            return back()->with('error', 'Gagal menghapus foto: ' . $e->getMessage());
        }
    }

    public function getRtsByRw(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdminDesa() && !$user->isSuperAdmin() && !$user->isAdminRw() && !$user->isAdminRt()) {
            return response()->json([], 403);
        }

        $rwId = $request->query('rw_id');
        if (!$rwId) {
            return response()->json([]);
        }

        $rts = Rt::where('rw_id', $rwId)
            ->where('desa_id', $user->desa_id)
            ->orderBy('nomor_rt')
            ->get(['id', 'nomor_rt']);

        return response()->json($rts);
    }

    public function indexPublic(Request $request)
    {
        $fasumsQuery = Fasum::with('desa', 'rw', 'rt', 'photos');

        $currentDesa = null;
        $allDesas = Desa::all();

        if ($request->filled('desa_id')) {
            $fasumsQuery->where('desa_id', $request->desa_id);
            $currentDesa = Desa::find($request->desa_id);
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $fasumsQuery->where(function ($q) use ($searchTerm) {
                $q->where('nama_fasum', 'like', '%' . $searchTerm . '%')
                    ->orWhere('alamat_lengkap', 'like', '%' . $searchTerm . '%');
            });
        }

        if ($request->filled('jenis_fasum')) {
            $fasumsQuery->where('jenis_fasum', $request->jenis_fasum);
        }

        $fasums = $fasumsQuery->latest()->paginate(10);

        return view('public.fasum.index', compact('fasums', 'currentDesa', 'allDesas'));
    }
}
