<section class="py-16 bg-gray-50" x-data="{ tab: 'kependudukan' }">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Heading -->
    <div class="text-center mb-10">
      <h3 class="text-sm text-blue-600 font-semibold uppercase tracking-wide">Solusi Digital</h3>
      <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mt-2">Satu Platform untuk Setiap Kebutuhan</h2>
    </div>

    <!-- Tabs Navigation -->
    <div class="flex justify-center space-x-2 mb-8">
      <button @click="tab = 'kependudukan'"
              :class="tab === 'kependudukan' ? 'bg-blue-600 text-white' : 'bg-white text-blue-600'"
              class="px-4 py-2 text-sm font-medium rounded-full border border-blue-600 hover:bg-blue-600 hover:text-white transition">
        Administrasi Kependudukan
      </button>
      <button @click="tab = 'kesehatan'"
              :class="tab === 'kesehatan' ? 'bg-blue-600 text-white' : 'bg-white text-blue-600'"
              class="px-4 py-2 text-sm font-medium rounded-full border border-blue-600 hover:bg-blue-600 hover:text-white transition">
        Kesehatan & Posyandu
      </button>
      <button @click="tab = 'keuangan'"
              :class="tab === 'keuangan' ? 'bg-blue-600 text-white' : 'bg-white text-blue-600'"
              class="px-4 py-2 text-sm font-medium rounded-full border border-blue-600 hover:bg-blue-600 hover:text-white transition">
        Keuangan & Aset
      </button>
    </div>

    <!-- Tabs Content -->
    <div class="space-y-12">
      <!-- Tab: Kependudukan -->
      <div x-show="tab === 'kependudukan'" x-transition class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
        <div>
          <h3 class="text-2xl font-bold text-gray-800 mb-4">Pelayanan Surat Cepat & Mandiri</h3>
          <p class="text-gray-600 mb-4">
            Sederhanakan alur birokrasi dengan layanan surat yang bisa diakses 24 jam melalui Anjungan Mandiri,
            mengurangi antrean dan beban kerja aparat desa.
          </p>
          <ul class="space-y-2 text-gray-700">
            <li class="flex items-center"><i class="fas fa-check-circle text-green-500 mr-2"></i> Anjungan Mandiri Cetak Surat</li>
            <li class="flex items-center"><i class="fas fa-check-circle text-green-500 mr-2"></i> Manajemen Data Warga & KK</li>
            <li class="flex items-center"><i class="fas fa-check-circle text-green-500 mr-2"></i> Histori Surat Terpusat</li>
          </ul>
        </div>
        <div class="text-center">
          <img src="{{ asset('images/features/pelayanan.png') }}" alt="Solusi Kependudukan"
               class="mx-auto max-w-full rounded-xl shadow-lg">
        </div>
      </div>

      <!-- Tab: Kesehatan -->
      <div x-show="tab === 'kesehatan'" x-transition class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
        <div>
          <h3 class="text-2xl font-bold text-gray-800 mb-4">Pantau Kesehatan Warga Secara Digital</h3>
          <p class="text-gray-600 mb-4">
            Fitur posyandu digital untuk pencatatan Z-score balita, deteksi stunting, dan pemantauan status gizi warga secara akurat dan cepat.
          </p>
          <ul class="space-y-2 text-gray-700">
            <li class="flex items-center"><i class="fas fa-check-circle text-green-500 mr-2"></i> Kalkulator Z-Score Otomatis</li>
            <li class="flex items-center"><i class="fas fa-check-circle text-green-500 mr-2"></i> Grafik Tumbuh Kembang</li>
            <li class="flex items-center"><i class="fas fa-check-circle text-green-500 mr-2"></i> Dashboard Deteksi Dini</li>
          </ul>
        </div>
        <div class="text-center">
          <img src="{{ asset('images/features/posyandu.png') }}" alt="Solusi Kesehatan"
               class="mx-auto max-w-full rounded-xl shadow-lg">
        </div>
      </div>

      <!-- Tab: Keuangan -->
      <div x-show="tab === 'keuangan'" x-transition class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
        <div>
          <h3 class="text-2xl font-bold text-gray-800 mb-4">Transparansi Keuangan & Aset Desa</h3>
          <p class="text-gray-600 mb-4">
            Catat transaksi, cetak laporan, dan kelola aset desa secara digital dengan sistem yang terintegrasi dan sesuai standar audit.
          </p>
          <ul class="space-y-2 text-gray-700">
            <li class="flex items-center"><i class="fas fa-check-circle text-green-500 mr-2"></i> Laporan Realisasi & APBDes</li>
            <li class="flex items-center"><i class="fas fa-check-circle text-green-500 mr-2"></i> Manajemen Aset Desa</li>
            <li class="flex items-center"><i class="fas fa-check-circle text-green-500 mr-2"></i> Integrasi Siskeudes</li>
          </ul>
        </div>
        <div class="text-center">
          <img src="{{ asset('images/features/laporan.png') }}" alt="Solusi Keuangan"
               class="mx-auto max-w-full rounded-xl shadow-lg">
        </div>
      </div>
    </div>
  </div>
</section>
