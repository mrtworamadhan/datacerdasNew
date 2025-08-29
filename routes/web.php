<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DesaController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\PerangkatDesaController;
use App\Http\Controllers\LembagaController;
use App\Http\Controllers\DesaProfileController;
use App\Http\Controllers\AdminDesaUserManagementController;
use App\Http\Controllers\UserDirectoryController;
use App\Http\Controllers\KartuKeluargaController;
use App\Http\Controllers\AnggotaKeluargaController;
use App\Http\Controllers\KategoriBantuanController;
use App\Http\Controllers\PenerimaBantuanController;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\FasumController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\JenisSuratController;
use App\Http\Controllers\SuratSettingController;
use App\Http\Controllers\PengajuanSuratController;
use App\Http\Controllers\WargaController;
use App\Http\Controllers\DataKesehatanAnakController;
use App\Http\Controllers\PemeriksaanAnakController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\LaporanKependudukanController;
use App\Http\Controllers\UniversalKegiatanController;
use App\Http\Controllers\AiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OcrController;
use App\Http\Controllers\CompanySettingController;
use App\Http\Controllers\PosyanduController;
use App\Http\Controllers\PosyanduReportController;
use App\Http\Controllers\SesiPosyanduController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\AsetController;
use App\Http\Controllers\KelompokController;
use App\Http\Controllers\LpjController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\CetakDokumenController;
use App\Http\Controllers\AnjunganController;
use App\Http\Controllers\WargaImportController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\PublicDesaController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\Portal\FasumController as PortalFasumController;
use App\Http\Controllers\Portal\PengajuanSuratController as PortalPengajuanSuratController;
use App\Http\Controllers\Portal\WargaController as PortalWargaController;
use App\Http\Controllers\Portal\PosyanduController as PortalPosyanduController;
use App\Http\Controllers\Portal\PenerimaBantuanController as PortalPenerimaBantuanController;
use App\Http\Controllers\Portal\KkController as PortalKkController;
use \App\Http\Controllers\Portal\ProfileController as PortalProfileController;


Route::domain('{subdomain}.' . config('app.url'))->group(function () {
    Route::get('/', [PublicDesaController::class, 'welcome'])->name('welcome');
    Route::get('/anjungan', [AnjunganController::class, 'index'])->name('anjungan.index');
    Route::post('/anjungan/verifikasi', [AnjunganController::class, 'verifikasi'])->name('anjungan.verifikasi');
    Route::get('/anjungan/pilih-surat', [AnjunganController::class, 'pilihSurat'])->name('anjungan.pilihSurat');
    Route::get('/anjungan/buat-surat/{jenisSurat}', [AnjunganController::class, 'buatSurat'])->name('anjungan.buatSurat');
    Route::post('/anjungan/proses-surat/{jenisSurat}', [AnjunganController::class, 'prosesSurat'])->name('anjungan.prosesSurat');
    Route::get('/anjungan/preview/{pengajuanSurat}', [AnjunganController::class, 'showPreview'])->name('anjungan.showPreview');
    Route::get('/anjungan/print/{pengajuanSurat}', [AnjunganController::class, 'printFinal'])->name('anjungan.printFinal');

    Route::middleware(['auth', 'verified', 'forbid_admin_dashboard'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('tenant.dashboard');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        Route::middleware('can:kelola profil')->group(function () {
            Route::get('/profil-desa', [DesaProfileController::class, 'edit'])->name('admin_desa.profile.edit');
            Route::put('/profil-desa', [DesaProfileController::class, 'update'])->name('admin_desa.profile.update');
            Route::resource('perangkat-desa', PerangkatDesaController::class);    
            Route::resource('lembaga', LembagaController::class);
            Route::resource('kelompok', KelompokController::class);

        });

        Route::middleware('can:kelola warga')->group(function () {
            Route::resource('kartu-keluarga', KartuKeluargaController::class);
            Route::post('/kartu-keluarga/{kartu_keluarga}/update-status-anggota', [KartuKeluargaController::class, 'updateStatusAnggota'])->name('kartu-keluarga.update-status-anggota');

            Route::get('/api/rts-by-rw', [KartuKeluargaController::class, 'getRtsByRw'])->name('api.rts-by-rw');
            Route::resource('kartu-keluarga.anggota', AnggotaKeluargaController::class)->except(['show']);

            Route::get('warga', [WargaController::class, 'index'])->name('warga.index'); // Admin RT ke atas bisa melihat
            Route::get('/warga/{warga}', [WargaController::class, 'show'])->name('warga.show');
            Route::post('/warga/{warga}/update-status', [WargaController::class, 'updateStatus'])->name('warga.update-status');

            // --- ROUTE BARU UNTUK IMPOR WARGA ---
            Route::get('/warga/impor', [WargaImportController::class, 'showImportForm'])->name('warga.import.form');
            Route::post('/warga/impor', [WargaImportController::class, 'import'])->name('warga.import.process');
            Route::get('/warga/impor/download-template', [WargaImportController::class, 'downloadTemplate'])->name('warga.import.template');
            Route::post('/warga/import/save', [WargaImportController::class, 'saveFromTemp'])->name('warga.import.save'); 

            Route::get('/wilayah', [WilayahController::class, 'index'])->name('wilayah.index');
            Route::get('/wilayah/rw/{rw}', [WilayahController::class, 'showRw'])->name('wilayah.showRw');
            Route::get('/wilayah/rt/{rt}', [WilayahController::class, 'showRt'])->name('wilayah.showRt');

            Route::get('/export/warga/semua', [WargaController::class, 'exportWargaSemua'])->name('warga.export.semua');
            Route::get('/export/warga/rw/{rw}', [WargaController::class, 'exportWargaPerRw'])->name('warga.export.per_rw');
            Route::get('/export/warga/rt/{rt}', [WargaController::class, 'exportWargaPerRt'])->name('warga.export.per_rt');

            Route::get('/laporan-kependudukan', [LaporanKependudukanController::class, 'index'])->name('laporan.kependudukan.index');
            Route::get('/laporan-kependudukan/export-excel', [LaporanKependudukanController::class, 'exportExcel'])->name('laporan.kependudukan.export-excel');
            Route::get('/laporan-status-khusus', [LaporanKependudukanController::class, 'laporanStatus'])->name('laporan.status-khusus.index');

        });

        Route::middleware('can:kelola kegiatan')->group(function () {
            Route::resource('lembaga', LembagaController::class);
            Route::resource('kelompok', KelompokController::class);

            Route::resource('kegiatans', UniversalKegiatanController::class);
            Route::get('/kegiatans/{kegiatan}/cetak-proposal', [UniversalKegiatanController::class, 'cetakProposal'])->name('kegiatans.cetakProposal');
            Route::get('/cetak/surat-pesanan/{pengeluaran}', [CetakDokumenController::class, 'cetakSuratPesanan'])->name('cetak.surat-pesanan');
            Route::get('/cetak/kwitansi/{pengeluaran}', [CetakDokumenController::class, 'cetakKwitansi'])->name('cetak.kwitansi');
            Route::get('/cetak/berita-acara/{pengeluaran}', [CetakDokumenController::class, 'cetakBeritaAcara'])->name('cetak.berita-acara');

            Route::get('/kegiatan/{kegiatan}/lpj/create', [LpjController::class, 'create'])->name('lpjs.create');
            Route::post('/kegiatan/{kegiatan}/lpj', [LpjController::class, 'store'])->name('lpjs.store');
            Route::get('/lpj/{lpj}/edit', [LpjController::class, 'edit'])->name('lpjs.edit');
            Route::put('/lpj/{lpj}', [LpjController::class, 'update'])->name('lpjs.update');
            Route::get('/lpj/kegiatan/{kegiatan}/cetak', [LpjController::class, 'generateLpj'])->name('lpj.generate');
            // Route::resource('lpjs', LpjController::class);
            Route::resource('pengeluarans', PengeluaranController::class)->only(['store', 'edit', 'update', 'destroy']);
        });

        Route::middleware('can:[buat proposal, setujui proposal, buat lpj]')->group(function () {
            // 
        });

        Route::middleware('can:kelola aset')->group(function () {

            Route::resource('asets', AsetController::class)->parameters(['asets' => 'aset']);
            Route::post('asets/find-code-by-ai', [AsetController::class, 'findCodeByAI'])->name('asets.findCodeByAI');
            Route::get('/export/asets/excel', [ExportController::class, 'exportAsetsExcel'])->name('export.asets.excel');
            Route::get('/export/asets/pdf', [ExportController::class, 'exportAsetsPdf'])->name('export.asets.pdf');
 
        });
        Route::middleware('can:kelola fasum')->group(function () {
            Route::resource('fasum', FasumController::class);
            Route::patch('/fasum/{fasum}/update-status', [FasumController::class, 'updateStatus'])->name('fasum.updateStatus');
            Route::delete('/fasum-photo/{photo}', [FasumController::class, 'destroyPhoto'])->name('fasum.destroyPhoto');

        });

        Route::middleware('can:kelola surat')->group(function () {
            Route::resource('jenis-surat', JenisSuratController::class);
            Route::post('/jenis-surat/preview', [JenisSuratController::class, 'preview'])->name('jenis-surat.preview');
            Route::get('/pengaturan-surat', [SuratSettingController::class, 'edit'])->name('surat-setting.edit');
            Route::put('/pengaturan-surat', [SuratSettingController::class, 'update'])->name('surat-setting.update');
            Route::resource('pengajuan-surat', PengajuanSuratController::class);
            Route::get('/api/jenis-surat/{jenisSurat}', [PengajuanSuratController::class, 'getJenisSuratDetails'])->name('api.jenis-surat.details');
            Route::post('/pengajuan-surat/{pengajuanSurat}/approve', [PengajuanSuratController::class, 'approveAndPrint'])->name('pengajuan-surat.approve');
            Route::post('/pengajuan-surat/{pengajuanSurat}/reject', [PengajuanSuratController::class, 'reject'])->name('pengajuan-surat.reject');
            Route::post('/pengajuan-surat/generate-pengantar', [PengajuanSuratController::class, 'generatePengantar'])->name('pengajuan-surat.generatePengantar');
            Route::get('/pengajuan-surat/{pengajuanSurat}/reprint', [PengajuanSuratController::class, 'reprint'])->name('pengajuan-surat.reprint');
 
        });

        Route::middleware('can:kelola bantuan')->group(function () {
            Route::get('/kategori-bantuan/{kategori_bantuan}/penerima/export-pdf', [PenerimaBantuanController::class, 'exportPdf'])->name('kategori-bantuan.penerima.exportPdf');
            Route::get('/kategori-bantuan/{kategori_bantuan}/penerima/export-excel', [PenerimaBantuanController::class, 'exportExcel'])->name('kategori-bantuan.penerima.exportExcel');
            Route::get('kategori-bantuan', [KategoriBantuanController::class, 'index'])->name('kategori-bantuan.index');
            // Hanya Admin Desa/Super Admin yang bisa CRUD kategori
            Route::post('kategori-bantuan', [KategoriBantuanController::class, 'store'])->name('kategori-bantuan.store');
            Route::get('kategori-bantuan/create', [KategoriBantuanController::class, 'create'])->name('kategori-bantuan.create');
            Route::get('kategori-bantuan/{kategori_bantuan}/edit', [KategoriBantuanController::class, 'edit'])->name('kategori-bantuan.edit');
            Route::put('kategori-bantuan/{kategori_bantuan}', [KategoriBantuanController::class, 'update'])->name('kategori-bantuan.update');
            Route::delete('kategori-bantuan/{kategori_bantuan}', [KategoriBantuanController::class, 'destroy'])->name('kategori-bantuan.destroy');

            // Rute untuk Manajemen Penerima Bantuan (Nested Resource di bawah Kategori Bantuan)
            // Admin RT ke atas bisa mengajukan dan melihat pengajuan/detail
            Route::get('kategori-bantuan/{kategori_bantuan}/penerima', [PenerimaBantuanController::class, 'index'])->name('kategori-bantuan.penerima.index');
            Route::get('kategori-bantuan/{kategori_bantuan}/penerima/create', [PenerimaBantuanController::class, 'create'])->name('kategori-bantuan.penerima.create');
            Route::post('kategori-bantuan/{kategori_bantuan}/penerima', [PenerimaBantuanController::class, 'store'])->name('kategori-bantuan.penerima.store');
            Route::get('kategori-bantuan/{kategori_bantuan}/penerima/{penerima}', [PenerimaBantuanController::class, 'show'])->name('kategori-bantuan.penerima.show');
            // Rute khusus untuk update status (Admin Desa, RW, RT)
            Route::post('kategori-bantuan/{kategori_bantuan}/penerima/{penerima}/update-status', [PenerimaBantuanController::class, 'updateStatus'])->name('kategori-bantuan.penerima.update-status'); // Admin RT ke atas bisa update status
            Route::delete('kategori-bantuan/{kategori_bantuan}/penerima/{penerima}', [PenerimaBantuanController::class, 'destroy'])->name('kategori-bantuan.penerima.destroy');
 
        });
        Route::middleware('can:kelola kesehatan')->group(function () {
            Route::get('/sesi-posyandu/{posyandu}/create', [SesiPosyanduController::class, 'create'])->name('sesi-posyandu.create');
            // Route untuk menandai anak sebagai "hadir"
            Route::post('/sesi-posyandu/hadir', [SesiPosyanduController::class, 'store'])->name('sesi-posyandu.store');
            Route::get('/export/posyandu/{posyandu}/anak-bermasalah/{tipeMasalah}', [ExportController::class, 'exportAnakBermasalah'])
                ->name('export.anak-bermasalah');
            Route::get('/export/pdf/posyandu/{posyandu}/anak-bermasalah/{tipeMasalah}', [ExportController::class, 'exportAnakBermasalahPdf'])
                ->name('export.anak-bermasalah.pdf');

            Route::resource('posyandu', PosyanduController::class);
            Route::get('posyandu-detail/{posyandu}', [PosyanduController::class, 'getDetail'])->name('posyandu.detail');
            Route::get('posyandu/{posyandu}/kaders', [PosyanduController::class, 'kaderManager'])->name('posyandu.kaders');
            Route::post('posyandu/{posyandu}/kaders', [PosyanduController::class, 'storeKader'])->name('posyandu.kaders.store');
            Route::delete('posyandu/kaders/{kader_id}', [PosyanduController::class, 'destroyKader'])->name('posyandu.kaders.destroy');
            Route::post('kesehatan-anak/assign', [DataKesehatanAnakController::class, 'assignPemeriksaan'])->name('kesehatan.anak.assign');
            Route::get('laporan/posyandu/{posyandu}/bulan/{bulan}/tahun/{tahun}', [PosyanduReportController::class, 'generatePdf'])->name('laporan.posyandu.pdf');

            Route::resource('kesehatan-anak', DataKesehatanAnakController::class);
            Route::resource('pemeriksaan-anak', PemeriksaanAnakController::class);
            Route::post('/pemeriksaan-anak/{dataKesehatanAnak}', [PemeriksaanAnakController::class, 'store'])->name('pemeriksaan-anak.store');

        });
        Route::middleware('can:kelola pengguna')->group(function () {
            
            Route::get('/manajemen-pengguna-desa', [AdminDesaUserManagementController::class, 'index'])->name('admin_desa.user_management.index');
            Route::get('/manajemen-pengguna-desa/generate', [AdminDesaUserManagementController::class, 'showGenerationForm'])->name('admin_desa.user_management.show_generation_form');
            Route::post('/manajemen-pengguna-desa/generate-perangkat-desa', [AdminDesaUserManagementController::class, 'generatePerangkatDesa'])->name('admin_desa.user_management.generate_perangkat_desa');
            Route::post('/manajemen-pengguna-desa/generate-rws', [AdminDesaUserManagementController::class, 'generateRws'])->name('admin_desa.user_management.generate_rws');
            Route::post('/manajemen-pengguna-desa/generate-rts', [AdminDesaUserManagementController::class, 'generateRts'])->name('admin_desa.user_management.generate_rts');
            Route::post('/manajemen-pengguna-desa/generate-kaders', [AdminDesaUserManagementController::class, 'generateKaders'])->name('admin_desa.user_management.generate_kaders');
            Route::get('/manajemen-pengguna-desa/{user}/edit', [AdminDesaUserManagementController::class, 'edit'])->name('admin_desa.user_management.edit');
            Route::put('/manajemen-pengguna-desa/{user}', [AdminDesaUserManagementController::class, 'update'])->name('admin_desa.user_management.update');
            Route::delete('/manajemen-pengguna-desa/{user}', [AdminDesaUserManagementController::class, 'destroy'])->name('admin_desa.user_management.destroy');
            Route::get('/profil-pengguna', [UserDirectoryController::class, 'index'])->name('admin_desa.user_directory.index');
            Route::post('/profil-pengguna/update-batch', [UserDirectoryController::class, 'updateBatch'])->name('admin_desa.user_directory.update_batch');
            Route::get('/manajemen-hak-akses', [\App\Http\Controllers\AdminDesa\UserPermissionController::class, 'index'])->name('permissions.index');
            Route::get('/manajemen-hak-akses/{userId}/edit', [\App\Http\Controllers\AdminDesa\UserPermissionController::class, 'edit'])->name('permissions.edit');
            Route::put('/manajemen-hak-akses/{userId}', [\App\Http\Controllers\AdminDesa\UserPermissionController::class, 'update'])->name('permissions.update');        });
            Route::post('/ai/generate-text', [AiController::class, 'generateReportText'])->name('ai.generate.text');
        
    });

    Route::middleware(['auth'])->prefix('portal')->name('portal.')->group(function () {
        Route::get('/dashboard', [PortalController::class, 'dashboard'])->name('dashboard');
        Route::patch('/fasum/{fasum}/update-status', [PortalFasumController::class, 'updateStatus'])->name('fasum.updateStatus');
        Route::resource('fasum', PortalFasumController::class);

        Route::get('/aset', [\App\Http\Controllers\Portal\AsetController::class, 'index'])->name('aset.index');
        Route::get('/laporan/kesehatan-anak', [\App\Http\Controllers\Portal\LaporanController::class, 'showKesehatanAnak'])->name('laporan.kesehatan_anak');
        Route::get('/laporan/demografi/{jenis}', [\App\Http\Controllers\Portal\LaporanController::class, 'showDemografi'])->name('laporan.demografi');

        Route::get('/surat', [PortalPengajuanSuratController::class, 'index'])->name('surat.index');
        Route::post('/surat', [PortalPengajuanSuratController::class, 'store'])->name('surat.store');
        Route::get('/surat/pilih-warga', [PortalPengajuanSuratController::class, 'create'])->name('surat.create');
        Route::get('/surat/pilih-jenis', [PortalPengajuanSuratController::class, 'pilihJenisSurat'])->name('surat.pilihJenis');
        Route::get('/surat/isi-detail', [PortalPengajuanSuratController::class, 'isiDetail'])->name('surat.isiDetail');
        Route::get('/surat/pengantar', [PortalPengajuanSuratController::class, 'buatPengantar'])->name('buat.pengantar');
        Route::post('/surat/pengantar', [PortalPengajuanSuratController::class, 'generatePengantar'])->name('surat.pengantar');

        Route::get('/warga', [PortalWargaController::class, 'index'])->name('warga.index');
        Route::get('/warga/{warga}/edit', [PortalWargaController::class, 'edit'])->name('warga.edit');
        Route::put('/warga/{warga}', [PortalWargaController::class, 'update'])->name('warga.update');
        Route::get('/warga/{warga}/editstatus', [PortalWargaController::class, 'editStatus'])->name('warga.editStatus');
        Route::put('/warga/{warga}', [PortalWargaController::class, 'updateStatus'])->name('warga.updateStatus');

        Route::resource('warga', PortalWargaController::class)->only(['index', 'edit', 'update']);

        Route::resource('kartuKeluarga', PortalKkController::class)->only(['index', 'edit', 'update']);

        Route::prefix('posyandu')->name('posyandu.')->group(function () {
            // Halaman utama/dashboard untuk kader (pemilihan bulan)
            Route::get('/', [PortalPosyanduController::class, 'index'])->name('index');
            Route::get('/sesi/{tahun}/{bulan}', [PortalPosyanduController::class, 'showSesi'])->name('sesi.show');
            Route::get('/pemeriksaan/input/{anak}', [PortalPosyanduController::class, 'createPemeriksaan'])->name('pemeriksaan.create');
            Route::post('/pemeriksaan', [PortalPosyanduController::class, 'storePemeriksaan'])->name('pemeriksaan.store');
            Route::get('/laporan', [PortalPosyanduController::class, 'laporan'])->name('laporan.index');
            Route::get('/laporan/generate/{tahun}/{bulan}', [PortalPosyanduController::class, 'generateLaporan'])->name('laporan.generate');
            Route::get('/find-anak', [PortalPosyanduController::class, 'findAnak'])->name('findAnak');
            Route::get('/sesi/{tahun}/{bulan}/find-anak', [PortalPosyanduController::class, 'findAnakBySesi'])->name('findAnakBySesi');
            Route::post('/store-anak-baru', [PortalPosyanduController::class, 'storeAnakBaru'])->name('store_anak_baru');
            Route::get('/rekam-medis', [PortalPosyanduController::class, 'showRekamMedisSearch'])->name('rekam_medis.search');
            Route::get('/rekam-medis/{kesehatanAnak}', [PortalPosyanduController::class, 'showRekamMedisDetail'])->name('rekam_medis.show');
            Route::get('/pemeriksaan/{pemeriksaan}/edit', [PortalPosyanduController::class, 'editPemeriksaan'])->name('pemeriksaan.edit');
            Route::put('/pemeriksaan/{pemeriksaan}', [PortalPosyanduController::class, 'updatePemeriksaan'])->name('pemeriksaan.update');
        });

        Route::prefix('bantuan')->name('bantuan.')->group(function () {
            // Halaman untuk memilih jenis bantuan
            Route::get('/', [PortalPenerimaBantuanController::class, 'pilihBantuan'])->name('pilihBantuan');
            Route::get('/{kategoriBantuan}/pilih-warga', [PortalPenerimaBantuanController::class, 'pilihWarga'])->name('pilihWarga');
            Route::post('/{kategoriBantuan}/store', [PortalPenerimaBantuanController::class, 'store'])->name('store');
        });

        Route::get('/profile', [PortalProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile/update-info', [PortalProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/update-password', [PortalProfileController::class, 'updatePassword'])->name('password.update');
        Route::delete('/profile/delete-account', [PortalProfileController::class, 'destroy'])->name('profile.destroy');

        Route::get('/laporan/kesejahteraan/{klasifikasi}', [\App\Http\Controllers\Portal\LaporanController::class, 'showByKesejahteraan'])->name('laporan.kesejahteraan');
        Route::get('/laporan/bantuan/{nama_kategori}', [\App\Http\Controllers\Portal\LaporanController::class, 'showByBantuan'])->name('laporan.bantuan');
        Route::get('/portal/laporan/belum-verifikasi', [\App\Http\Controllers\Portal\LaporanController::class, 'showBelumVerifikasi'])->name('laporan.belum_verifikasi');
        Route::get('/portal/laporan/tidak-lengkap', [\App\Http\Controllers\Portal\LaporanController::class, 'showTidakLengkap'])->name('laporan.tidak_lengkap');

    });

    Route::middleware(['auth'])->group(function () {
        //APIROUTE
        Route::get('/api/warga', [WargaController::class, 'searchWarga'])->name('search.warga');
        Route::get('/api/kk', [WargaController::class, 'searchKK'])->name('search.kkFast');
        Route::get('/search/kartu-keluarga', [PenerimaBantuanController::class, 'searchKK'])->name('search.kk');
        Route::get('/api/search-warga', [PenerimaBantuanController::class, 'searchWargaPenerima'])->name('search.penerimaWarga');
    });

    require __DIR__ . '/auth.php';
});

Route::get('/', function () {
    return view('welcome');
});

Route::get('/about-us', function () {
    return view('public.about_us');
})->name('about.us');

Route::get('/features', function () {
    return view('public.features');
})->name('features');

Route::get('/subscription-expired', function () {
    return view('auth.subscription-expired');
})->name('subscription.expired');

// Halaman Kebijakan Privasi
Route::get('/privacy-policy', function () {
    return view('public.privacy-policy');
})->name('privacy.policy');

// Halaman Syarat & Ketentuan Layanan
Route::get('/terms-of-service', function () {
    return view('public.terms-of-service');
})->name('terms.of.service');

Route::get('/desa-public', [PublicController::class, 'indexDesa'])->name('public.desas.index');
Route::get('/fasum-public', [PublicController::class, 'indexPublic'])->name('public.fasum.index');
Route::get('/fasilitas/{fasum}', [PublicController::class, 'showFasum'])->name('public.fasum.show');
Route::post('/api/ocr/ktp', [OcrController::class, 'scanKtpOcr'])->name('api.ocr.ktp'); // Admin RT ke atas bisa scan

Route::get('/get-provinces', [WilayahController::class, 'getProvinces']);
Route::get('/get-cities/{province_id}', [WilayahController::class, 'getCities']);
Route::get('/get-subdistricts/{city_id}', [WilayahController::class, 'getSubdistricts']);
Route::get('/get-villages/{subdistrict_id}', [WilayahController::class, 'getVillages']);

// --- Rute Super Admin ---
Route::middleware(['auth', 'is_super_admin'])->group(function () { // Kita akan buat middleware 'is_super_admin' nanti
    Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('superadmin.dashboard');
    Route::resource('desas', DesaController::class);
    Route::resource('admin/users', AdminUserController::class)->names('admin.users');
    Route::get('company-settings', [CompanySettingController::class, 'index'])->name('company-settings.index');
    Route::put('company-settings', [CompanySettingController::class, 'update'])->name('company-settings.update');
    Route::get('profile', [\App\Http\Controllers\SuperAdmin\ProfileController::class, 'edit'])->name('superadmin.profile.edit');
    Route::put('profile/password', [\App\Http\Controllers\SuperAdmin\ProfileController::class, 'updatePassword'])->name('superadmin.profile.password.update');

});

require __DIR__ . '/auth.php';



