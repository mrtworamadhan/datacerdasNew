<section class="w-full py-12 bg-white">
    <div class="max-w-6xl mx-auto px-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Card 1 -->
            <div>
                <a href="{{-- route('features.kependudukan') --}}" class="block hover:shadow-lg transition">
                    <div class="bg-white p-6 rounded-xl shadow-md flex flex-col items-center text-center">
                        <img src="{{ asset('images/features/lpj.png') }}" alt="Administrasi Kependudukan"
                            class="w-full h-full object-cover">
                        <div class="p-5">
                            <div class="flex flex-wrap gap-2 mb-3">
                                <span
                                    class="bg-blue-100 text-blue-800 text-xs font-medium px-3 py-1 rounded-full">Lembaga
                                    Cerdas</span>
                                <span
                                    class="bg-blue-100 text-blue-800 text-xs font-medium px-3 py-1 rounded-full">Administrasi
                                    Cerdas</span>
                                <span
                                    class="bg-blue-100 text-blue-800 text-xs font-medium px-3 py-1 rounded-full">Proposal
                                    & LPJ Cerdas</span>
                            </div>
                            <h3 class="text-xl font-semibold mb-2">Manajemen Lembaga & Laporan</h3>
                            <p class="text-gray-600 text-sm">
                                Buat proposal, catat keuangan, dan generate LPJ profesional secara otomatis dengan
                                bantuan
                                AI.
                            </p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Card 2 -->
            <div>
                <a href="{{-- route('features.kesehatan') --}}" class="block hover:shadow-lg transition">
                    <div class="bg-white p-6 rounded-xl shadow-md flex flex-col items-center text-center">
                        <img src="{{ asset('images/features/kesehatan.png') }}" alt="Kesehatan & Aset Desa"
                            class="w-full h-full object-cover">
                        <div class="p-5">
                            <div class="flex flex-wrap gap-2 mb-3">
                                <span
                                    class="bg-green-100 text-green-800 text-xs font-medium px-3 py-1 rounded-full">Posyandu
                                    Cerdas</span>
                                <span
                                    class="bg-green-100 text-green-800 text-xs font-medium px-3 py-1 rounded-full">Kalkulator
                                    Z-Score</span>
                                <span
                                    class="bg-green-100 text-green-800 text-xs font-medium px-3 py-1 rounded-full">Deteksi
                                    Stunting</span>
                            </div>
                            <h3 class="text-xl font-semibold mb-2">Pantau Aset & Kesehatan Warga</h3>
                            <p class="text-gray-600 text-sm">
                                Monitor kesehatan balita dan kelola inventaris aset desa dengan kodifikasi standar
                                nasional.
                            </p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Card 3 -->
            <div>
                <a href="{{-- route('features.keuangan') --}}" class="block hover:shadow-lg transition">
                    <div class="bg-white p-6 rounded-xl shadow-md flex flex-col items-center text-center">
                        <img src="{{ asset('images/features/kependudukan.png') }}" alt="Administrasi Kependudukan"
                            class="w-full h-full object-cover">
                        <div class="p-5">
                            <div class="flex flex-wrap gap-2 mb-3">
                                <span
                                    class="bg-yellow-100 text-yellow-800 text-xs font-medium px-3 py-1 rounded-full">Data
                                    Warga Cerdas</span>
                                <span
                                    class="bg-yellow-100 text-yellow-800 text-xs font-medium px-3 py-1 rounded-full">Pelayanan
                                    Cerdas</span>
                                <span
                                    class="bg-yellow-100 text-yellow-800 text-xs font-medium px-3 py-1 rounded-full">Anjungan
                                    Mandiri</span>
                            </div>
                            <h3 class="text-xl font-semibold mb-2">Administrasi Cepat & Akurat</h3>
                            <p class="text-gray-600 text-sm">
                                Kelola data kependudukan dengan mudah dan berikan layanan surat mandiri untuk
                                warga.
                            </p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="w-full mt-8 text-center">
            <a href="{{ route('features') }}"
                class="inline-flex items-center gap-2 text-blue-600 hover:underline font-semibold text-lg">
                Pelajari Semua Fitur <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>