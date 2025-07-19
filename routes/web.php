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
use App\Http\Controllers\AiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OcrController;
use App\Http\Controllers\CompanySettingController;


Route::get('/phpinfo', function() { phpinfo(); });
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

Route::get('/dashboard', [DashboardController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// --- Rute Super Admin ---
Route::middleware(['auth', 'is_super_admin'])->group(function () { // Kita akan buat middleware 'is_super_admin' nanti
    Route::resource('desas', DesaController::class);
    Route::resource('admin/users', AdminUserController::class)->names('admin.users');
    Route::get('company-settings', [CompanySettingController::class, 'index'])->name('company-settings.index');
    Route::put('company-settings', [CompanySettingController::class, 'update'])->name('company-settings.update');
});

Route::middleware(['auth'])->group(function () {
    // Nanti kita tambahkan Gate atau Policy untuk membatasi akses ke admin_desa saja
    Route::get('/profil-desa', [DesaProfileController::class, 'edit'])->name('admin_desa.profile.edit');
    Route::put('/profil-desa', [DesaProfileController::class, 'update'])->name('admin_desa.profile.update');
    Route::resource('perangkat-desa', PerangkatDesaController::class);
    Route::resource('lembaga', LembagaController::class);

    Route::get('/manajemen-pengguna-desa', [AdminDesaUserManagementController::class, 'index'])->name('admin_desa.user_management.index');
    Route::get('/manajemen-pengguna-desa/generate', [AdminDesaUserManagementController::class, 'showGenerationForm'])->name('admin_desa.user_management.show_generation_form');
    Route::post('/manajemen-pengguna-desa/generate-rws', [AdminDesaUserManagementController::class, 'generateRws'])->name('admin_desa.user_management.generate_rws');
    Route::post('/manajemen-pengguna-desa/generate-rts', [AdminDesaUserManagementController::class, 'generateRts'])->name('admin_desa.user_management.generate_rts');
    Route::post('/manajemen-pengguna-desa/generate-kaders', [AdminDesaUserManagementController::class, 'generateKaders'])->name('admin_desa.user_management.generate_kaders');
    Route::get('/manajemen-pengguna-desa/{user}/edit', [AdminDesaUserManagementController::class, 'edit'])->name('admin_desa.user_management.edit');
    Route::put('/manajemen-pengguna-desa/{user}', [AdminDesaUserManagementController::class, 'update'])->name('admin_desa.user_management.update');
    Route::delete('/manajemen-pengguna-desa/{user}', [AdminDesaUserManagementController::class, 'destroy'])->name('admin_desa.user_management.destroy');
    Route::get('/profil-pengguna', [UserDirectoryController::class, 'index'])->name('admin_desa.user_directory.index');
    Route::post('/profil-pengguna/update-batch', [UserDirectoryController::class, 'updateBatch'])->name('admin_desa.user_directory.update_batch');

    Route::resource('kartu-keluarga', KartuKeluargaController::class);
    Route::get('/api/rts-by-rw', [KartuKeluargaController::class, 'getRtsByRw'])->name('api.rts-by-rw');
    Route::resource('kartu-keluarga.anggota', AnggotaKeluargaController::class)->except(['show']);

    Route::get('warga', [WargaController::class, 'index'])->name('warga.index')->middleware('can:admin_rt_access'); // Admin RT ke atas bisa melihat

    Route::get('/kategori-bantuan/{kategori_bantuan}/penerima/export-pdf', [PenerimaBantuanController::class, 'exportPdf'])->name('kategori-bantuan.penerima.exportPdf')->middleware('can:admin_desa_access');
    Route::get('/kategori-bantuan/{kategori_bantuan}/penerima/export-excel', [PenerimaBantuanController::class, 'exportExcel'])->name('kategori-bantuan.penerima.exportExcel')->middleware('can:admin_desa_access');
    Route::get('kategori-bantuan', [KategoriBantuanController::class, 'index'])->name('kategori-bantuan.index')->middleware('can:admin_rt_access');
    // Hanya Admin Desa/Super Admin yang bisa CRUD kategori
    Route::post('kategori-bantuan', [KategoriBantuanController::class, 'store'])->name('kategori-bantuan.store')->middleware('can:admin_desa_access');
    Route::get('kategori-bantuan/create', [KategoriBantuanController::class, 'create'])->name('kategori-bantuan.create')->middleware('can:admin_desa_access');
    Route::get('kategori-bantuan/{kategori_bantuan}/edit', [KategoriBantuanController::class, 'edit'])->name('kategori-bantuan.edit')->middleware('can:admin_desa_access');
    Route::put('kategori-bantuan/{kategori_bantuan}', [KategoriBantuanController::class, 'update'])->name('kategori-bantuan.update')->middleware('can:admin_desa_access');
    Route::delete('kategori-bantuan/{kategori_bantuan}', [KategoriBantuanController::class, 'destroy'])->name('kategori-bantuan.destroy')->middleware('can:admin_desa_access');

    // Rute untuk Manajemen Penerima Bantuan (Nested Resource di bawah Kategori Bantuan)
    // Admin RT ke atas bisa mengajukan dan melihat pengajuan/detail
    Route::get('kategori-bantuan/{kategori_bantuan}/penerima', [PenerimaBantuanController::class, 'index'])->name('kategori-bantuan.penerima.index')->middleware('can:admin_rt_access');
    Route::get('kategori-bantuan/{kategori_bantuan}/penerima/create', [PenerimaBantuanController::class, 'create'])->name('kategori-bantuan.penerima.create')->middleware('can:admin_rt_access');
    Route::post('kategori-bantuan/{kategori_bantuan}/penerima', [PenerimaBantuanController::class, 'store'])->name('kategori-bantuan.penerima.store')->middleware('can:admin_rt_access');
    Route::get('kategori-bantuan/{kategori_bantuan}/penerima/{penerima}', [PenerimaBantuanController::class, 'show'])->name('kategori-bantuan.penerima.show')->middleware('can:admin_rt_access');
    // Rute khusus untuk update status (Admin Desa, RW, RT)
    Route::post('kategori-bantuan/{kategori_bantuan}/penerima/{penerima}/update-status', [PenerimaBantuanController::class, 'updateStatus'])->name('kategori-bantuan.penerima.update-status')->middleware('can:admin_rt_access'); // Admin RT ke atas bisa update status
    Route::delete('kategori-bantuan/{kategori_bantuan}/penerima/{penerima}', [PenerimaBantuanController::class, 'destroy'])->name('kategori-bantuan.penerima.destroy')->middleware('can:admin_desa_access');
 
    Route::get('/wilayah', [WilayahController::class, 'index'])->name('wilayah.index');
    Route::get('/wilayah/rw/{rw}', [WilayahController::class, 'showRw'])->name('wilayah.showRw');
    Route::get('/wilayah/rt/{rt}', [WilayahController::class, 'showRt'])->name('wilayah.showRt');

    Route::resource('fasum', FasumController::class);
    Route::patch('/fasum/{fasum}/update-status', [FasumController::class, 'updateStatus'])->name('fasum.updateStatus');
    Route::delete('/fasum-photo/{photo}', [FasumController::class, 'destroyPhoto'])->name('fasum.destroyPhoto');

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

    //APIROUTE
    Route::get('/api/warga', [WargaController::class, 'searchWarga'])->name('search.warga');
    Route::get('/search/kartu-keluarga', [PenerimaBantuanController::class, 'searchKK'])->name('search.kk');
    Route::get('/api/search-warga', [PenerimaBantuanController::class, 'searchWargaPenerima'])->name('search.penerimaWarga');
    Route::post('/api/ocr/ktp', [OcrController::class, 'scanKtpOcr'])->name('api.ocr.ktp')->middleware('can:admin_rt_access'); // Admin RT ke atas bisa scan

    Route::resource('kesehatan-anak', DataKesehatanAnakController::class);
    Route::post('/pemeriksaan-anak/{dataKesehatanAnak}', [PemeriksaanAnakController::class, 'store'])->name('pemeriksaan-anak.store');

    Route::resource('lembaga/{lembaga}/kegiatan', KegiatanController::class)->names('lembaga.kegiatan');
    Route::delete('/kegiatan-photo/{photo}', [KegiatanController::class, 'destroyPhoto'])->name('kegiatan.photo.destroy');
    Route::get('/lembaga/{lembaga}/kegiatan/{kegiatan}/cetak', [KegiatanController::class, 'cetakLaporan'])->name('lembaga.kegiatan.cetak');
    Route::post('/ai/generate-text', [AiController::class, 'generateReportText'])->name('ai.generate.text');
});


require __DIR__ . '/auth.php';
