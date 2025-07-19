<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Cerdas - Desa Tertata dengan Cerdas</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {

                        primary: {
                            DEFAULT: '#0d7e7eff',
                            '50': '#E0F2F2',
                            '100': '#B3E0E0',
                            '200': '#80CCCC',
                            '300': '#4DB8B8',
                            '400': '#26A3A3',
                            '500': '#10A8A8',
                            '600': '#0C8A8A',
                            '700': '#086C6C',
                            '800': '#044D4D',
                            '900': '#022E2E',
                            '950': '#011717',
                        },
                        secondary: '#6B7280',
                        accent: '#F59E0B',
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }

        main {
            padding-top: 72px;
        }

        .hero-section {
            background-image: url('{{ asset('images/welcome/bg-welcome.png') }}');
            background-size: cover;
            background-position: right;
            position: relative;
            min-height: 550px;

        }

        @media (min-width: 640px) {
            .overlay {
                padding-left: 2rem;
                padding-right: 2rem;
            }
        }

        @media (min-width: 768px) {
            .overlay {
                padding-left: 5%;
                padding-right: 5%;
            }
        }

        .hero-content {
            max-width: 4xl;
            text-align: left;
            color: '#064e4eff';
            z-index: 10;
        }


        .card-shadow {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08);
        }

        .navbar-logo {
            height: 40px;
            width: auto;
            margin-right: 12px;
            border-radius: 8px;
        }

        .navbar-logoFont {
            height: 30px;
            width: auto;
            margin-right: 12px;
        }

        .feature-icon {
            font-size: 4rem;
            /* Ukuran ikon di bagian fitur, diperbesar */
            margin-bottom: 1rem;
        }

        .how-it-works-icon {
            /* Kelas baru untuk ikon di bagian cara kerja */
            font-size: 4rem;
            /* UKURAN IKON DIPERBESAR DI SINI */
            margin-bottom: 1rem;
            color: #10A8A8;
            /* Warna primary */
        }
    </style>
</head>

<body class="antialiased">
    <div class="min-h-screen flex flex-col">
        <!-- Navbar -->
        <nav class="bg-white shadow-md p-4 flex justify-between items-center fixed w-full z-10 rounded-b-lg">
            <div class="flex items-center">
                <a href="/" class="flex items-center">
                    <img src="{{ asset('images/logo/logo only trp.png') }}" alt="Logo DATA CERDAS" class="navbar-logo">
                    <img src="{{ asset('images/logo/logo line.png') }}" alt="DATA CERDAS" class="navbar-logoFont">
                </a>
            </div>
            <div>
                @if (Route::has('login'))
                    <div class="space-x-4 hidden md:flex"> {{-- Sembunyikan di mobile, tampilkan di desktop --}}
                        <a href="{{ route('about.us') }}"
                            class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors duration-300">About
                            Us</a>
                        <a href="{{ route('public.desas.index') }}"
                            class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors duration-300">Desa
                            Cerdas</a>
                        <a href="{{ route('public.fasum.index') }}"
                            class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors duration-300">Fasum
                            Cerdas</a>
                        <a href="{{ route('features') }}"
                            class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors duration-300">Fitur</a>
                        <a href="{{ route('privacy.policy') }}"
                            class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors duration-300">Kebijakan</a>
                        <a href="{{ route('terms.of.service') }}"
                            class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors duration-300">Ketentuan Layanan</a>
                        @auth
                            <a href="{{ url('/dashboard') }}"
                                class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-primary hover:bg-primary-600 transition-colors duration-300">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}"
                                class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-primary hover:bg-primary-600 transition-colors duration-300">Log
                                in</a>
                        @endauth
                    </div>
                    {{-- Burger menu for mobile --}}
                    <button class="md:hidden text-gray-700 focus:outline-none" id="mobile-menu-button">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                @endif
            </div>
        </nav>

        {{-- Mobile Menu (Hidden by default) --}}
        <div class="fixed top-0 left-0 w-full h-full bg-white z-20 flex flex-col items-center justify-center space-y-6 md:hidden"
            id="mobile-menu" style="display: none;">
            <button class="absolute top-4 right-4 text-gray-700 focus:outline-none" id="close-mobile-menu">
                <i class="fas fa-times text-3xl"></i>
            </button>
            <a href="{{ route('about.us') }}"
                class="text-xl font-medium text-gray-800 hover:text-primary transition-colors duration-300">About Us</a>
            <a href="{{ route('public.desas.index') }}"
                class="text-xl font-medium text-gray-800 hover:text-primary transition-colors duration-300">Desa
                Cerdas</a>
            <a href="{{ route('public.fasum.index') }}"
                class="text-xl font-medium text-gray-800 hover:text-primary transition-colors duration-300">Fasum
                Cerdas</a>
            <a href="{{ route('features') }}"
                class="text-xl font-medium text-gray-800 hover:text-primary transition-colors duration-300">Fitur</a>
            <a href="{{ route('privacy.policy') }}"
                class="text-xl font-medium text-gray-800 hover:text-primary transition-colors duration-300">Kebijakan</a>
             <a href="{{ route('terms.of.service') }}"
                class="text-xl font-medium text-gray-800 hover:text-primary transition-colors duration-300">Ketentuan Layanan</a>
            @auth
                <a href="{{ url('/dashboard') }}"
                    class="px-6 py-3 bg-primary text-white font-semibold rounded-full hover:bg-primary-600 transition-colors duration-300">Dashboard</a>
            @else
                <a href="{{ route('login') }}"
                    class="px-6 py-3 bg-primary text-white font-semibold rounded-full hover:bg-primary-600 transition-colors duration-300">Log
                    in</a>
            @endauth
        </div>
        <main>
            <header class="hero-section rounded-b-lg shadow-lg">
                <div class="overlay rounded-b-lg px-4 sm:px-6 md:px-8 lg:px-12 py-10 sm:py-16">
                    <div
                        class="hero-content w-full text-left flex flex-col sm:flex-row items-center sm:items-start gap-6">
                        <div class="w-full sm:w-2/3">
                            <img src="{{ asset('images/logo/logo line.png') }}" alt="Logo Desa Cerdas"
                                class="w-32 sm:w-40 md:w-48 mx-50 sm:mx-50 mb-5">
                            <h1
                                class="text-2xl sm:text-3xl md:text-5xl font-bold leading-snug sm:leading-tight mb-4 sm:mb-6">
                                AKSELERASI PROGRAM DESA CERDAS MELALUI TATA KELOLA PEMERINTAHAN DIGITAL
                            </h1>
                            <p class="text-sm sm:text-base md:text-lg mb-6 sm:mb-8">
                                Transformasi Digital untuk Pemerintahan Desa yang Modern.
                            </p>
                            @if (Route::has('login'))
                                @auth
                                    <a href="{{ url('/dashboard') }}"
                                        class="px-6 py-2 sm:px-8 sm:py-3 bg-accent text-white font-semibold rounded-full hover:bg-yellow-600 transition-all duration-300 inline-block">
                                        Menuju Dashboard
                                    </a>
                                @else
                                    <a href="{{ route('login') }}"
                                        class="px-6 py-2 sm:px-8 sm:py-3 bg-primary text-white font-semibold rounded-full hover:bg-primary-600 transition-all duration-300 inline-block">
                                        Mulai Sekarang
                                    </a>
                                @endauth
                            @endif
                        </div>
                    </div>
                </div>
            </header>

            <!-- Why DATA CERDAS Section -->
            <section class="py-16 bg-gray-100 rounded-lg shadow-inner mt-12">
                <div class="container mx-auto px-4 text-center">
                    <h2 class="text-3xl font-bold text-gray-800 mb-12">Mengapa DATA CERDAS?</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div
                            class="p-6 bg-white rounded-xl card-shadow transform hover:scale-105 transition-transform duration-300">
                            <i class="fas fa-chart-line feature-icon text-primary"></i>
                            <h3 class="text-xl font-semibold text-gray-800 mb-3">Efisiensi & Akurasi Data</h3>
                            <p class="text-gray-600">Transformasi administrasi manual menjadi digital, mengurangi
                                kesalahan dan mempercepat proses.</p>
                        </div>
                        <div
                            class="p-6 bg-white rounded-xl card-shadow transform hover:scale-105 transition-transform duration-300">
                            <i class="fas fa-shield-alt feature-icon text-primary"></i>
                            <h3 class="text-xl font-semibold text-gray-800 mb-3">Transparansi & Akuntabilitas</h3>
                            <p class="text-gray-600">Alur kerja yang jelas dan tercatat, meningkatkan kepercayaan publik
                                terhadap tata kelola desa.</p>
                        </div>
                        <div
                            class="p-6 bg-white rounded-xl card-shadow transform hover:scale-105 transition-transform duration-300">
                            <i class="fas fa-handshake feature-icon text-primary"></i>
                            <h3 class="text-xl font-semibold text-gray-800 mb-3">Pelayanan Publik Lebih Baik</h3>
                            <p class="text-gray-600">Akses informasi dan pengajuan yang lebih mudah bagi warga,
                                meningkatkan kualitas layanan desa.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Comprehensive Features Section -->
            <section class="py-16 bg-white rounded-lg shadow-lg mx-auto w-11/12 mt-12 relative z-10">
                <div class="container mx-auto px-4">
                    <h2 class="text-3xl font-bold text-center text-gray-800 mb-12">Fitur Lengkap DATA CERDAS</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                        <div
                            class="p-6 rounded-xl card-shadow text-center transform hover:scale-105 transition-transform duration-300 bg-blue-light border border-blue-200">
                            <i class="fas fa-cogs feature-icon text-blue-600"></i>
                            <h3 class="text-xl font-semibold text-gray-800 mb-3">Cerdas Administrasi & Pengguna</h3>
                            <p class="text-gray-600">Kelola profil desa, perangkat, serta manajemen akun admin desa, RW,
                                RT, dan kader secara terpusat.</p>
                        </div>
                        <div
                            class="p-6 rounded-xl card-shadow text-center transform hover:scale-105 transition-transform duration-300 bg-blue-light border border-blue-200">
                            <i class="fas fa-users feature-icon text-blue-600"></i>
                            <h3 class="text-xl font-semibold text-gray-800 mb-3">Cerdas Data Warga</h3>
                            <p class="text-gray-600">Kelola data Kartu Keluarga dan warga secara detail, terpusat, dan
                                terintegrasi dengan wilayah RW/RT.</p>
                        </div>
                        <div
                            class="p-6 rounded-xl card-shadow text-center transform hover:scale-105 transition-transform duration-300 bg-green-light border border-green-200">
                            <i class="fas fa-handshake feature-icon text-green-600"></i>
                            <h3 class="text-xl font-semibold text-gray-800 mb-3">Cerdas Tata Lembaga & Kegiatan</h3>
                            <p class="text-gray-600">Manajemen data lembaga desa, perangkat, dan pengelolaan kegiatan
                                desa secara terstruktur dan transparan.</p>
                        </div>
                        <div
                            class="p-6 rounded-xl card-shadow text-center transform hover:scale-105 transition-transform duration-300 bg-purple-light border border-purple-200">
                            <i class="fas fa-file-alt feature-icon text-purple-600"></i>
                            <h3 class="text-xl font-semibold text-gray-800 mb-3">Cerdas Administrasi Surat</h3>
                            <p class="text-gray-600">Buat template surat dinamis, ajukan permohonan, proses verifikasi,
                                dan cetak surat resmi secara otomatis.</p>
                        </div>
                        <div
                            class="p-6 rounded-xl card-shadow text-center transform hover:scale-105 transition-transform duration-300 bg-yellow-light border border-yellow-200">
                            <i class="fas fa-building feature-icon text-yellow-600"></i>
                            <h3 class="text-xl font-semibold text-gray-800 mb-3">Cerdas Tata Fasum</h3>
                            <p class="text-gray-600">Pendataan dan pengelolaan Fasum desa, lengkap dengan detail lokasi
                                dan kondisi.</p>
                        </div>
                        <div
                            class="p-6 rounded-xl card-shadow text-center transform hover:scale-105 transition-transform duration-300 bg-red-light border border-red-200">
                            <i class="fas fa-child feature-icon text-red-600"></i>
                            <h3 class="text-xl font-semibold text-gray-800 mb-3">Cerdas Data Kesehatan</h3>
                            <p class="text-gray-600">Manajemen data kesehatan ibu hamil, balita, dan anak untuk
                                pemantauan gizi serta program Posyandu yang efektif.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- AI Features Section -->
            <section class="py-16 bg-gray-100 mt-12 rounded-lg shadow-inner">
                <div class="container mx-auto px-4 text-center">
                    <h2 class="text-3xl font-bold text-gray-800 mb-12">Integrasi AI Canggih</h2>
                    <div class="grid grid-cols-1 md:grid-cols-1 lg:grid-cols-1 gap-8">
                        <div
                            class="p-6 bg-indigo-light rounded-xl card-shadow transform hover:scale-105 transition-transform duration-300">
                            <i class="fas fa-robot feature-icon text-indigo-600"></i>
                            <h3 class="text-xl font-semibold text-gray-800 mb-3">Kecerdasan Buatan untuk Efisiensi
                                Maksimal</h3>
                            <p class="text-gray-600">
                                DATA CERDAS memanfaatkan teknologi AI untuk:
                            <ul class="list-disc list-inside mt-2 text-gray-600">
                                <li>Pembuatan Proposal & RAB Otomatis: Menyusun draf proposal kegiatan dan Rencana
                                    Anggaran Biaya (RAB) secara cerdas.</li>
                                <li>Deteksi Potensi Desa: Menganalisis data desa untuk mengidentifikasi potensi dan
                                    peluang pengembangan.</li>
                                <li>Pembuatan Profil Desa Berbasis AI: Menggenerasi narasi profil desa yang komprehensif
                                    dari data yang tersedia.</li>
                                <li>OCR KTP Otomatis: Mempercepat input data warga dengan membaca informasi langsung
                                    dari KTP.</li>
                            </ul>
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="py-16 bg-gray-100 mt-12 rounded-lg shadow-inner">
                <div class="container mx-auto px-4 text-center">
                    <h2 class="text-3xl font-bold text-gray-800 mb-12">Bagaimana DATA CERDAS Bekerja?</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="p-6 bg-white rounded-xl card-shadow">
                            <div class="text-primary how-it-works-icon"><i class="fas fa-user-plus"></i></div> {{--
                            Ikon: Daftar & Aktivasi --}}
                            <h3 class="text-xl font-semibold text-gray-800 mb-3">Daftar & Aktivasi Desa</h3>
                            <p class="text-gray-600">Super Admin mendaftarkan desa Anda dan mengaktifkan akun Admin
                                Desa.</p>
                        </div>
                        <div class="p-6 bg-white rounded-xl card-shadow">
                            <div class="text-primary how-it-works-icon"><i class="fas fa-database"></i></div> {{-- Ikon:
                            Input Data --}}
                            <h3 class="text-xl font-semibold text-gray-800 mb-3">Input Data Komprehensif</h3>
                            <p class="text-gray-600">Admin Desa, RW, RT, dan Kader mulai memasukkan data kependudukan,
                                fasum, bantuan, dan lainnya.</p>
                        </div>
                        <div class="p-6 bg-white rounded-xl card-shadow">
                            <div class="text-primary how-it-works-icon"><i class="fas fa-rocket"></i></div> {{-- Ikon:
                            Nikmati --}}
                            <h3 class="text-xl font-semibold text-gray-800 mb-3">Nikmati Kemudahan Tata Kelola</h3>
                            <p class="text-gray-600">Manfaatkan fitur-fitur canggih untuk efisiensi administrasi dan
                                pelayanan publik.</p>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Contact Section -->
        <section class="py-16 bg-white rounded-lg shadow-lg mx-auto w-11/12 mt-12 relative z-10">
            <div class="container mx-auto px-4 text-center">
                <h2 class="text-3xl font-bold text-gray-800 mb-8">Hubungi Kami</h2>
                <p class="text-gray-700 max-w-3xl mx-auto leading-relaxed mb-6">
                    Tertarik untuk membawa DATA CERDAS ke desa Anda? Hubungi kami untuk demo atau informasi lebih
                    lanjut.
                </p>
                <div class="flex flex-col sm:flex-row justify-center items-center space-y-4 sm:space-y-0 sm:space-x-6">
                    @if($companySettings['whatsapp_number'] ?? null)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $companySettings['whatsapp_number']) }}"
                            target="_blank"
                            class="px-6 py-3 bg-green-500 text-white font-semibold rounded-full hover:bg-green-600 transition-colors duration-300 transform hover:scale-105 inline-flex items-center">
                            <i class="fab fa-whatsapp mr-2"></i> WhatsApp
                        </a>
                    @endif
                    @if($companySettings['company_email'] ?? null)
                        <a href="mailto:{{ $companySettings['company_email'] }}"
                            class="px-6 py-3 bg-red-500 text-white font-semibold rounded-full hover:bg-red-600 transition-colors duration-300 transform hover:scale-105 inline-flex items-center">
                            <i class="fas fa-envelope mr-2"></i> Email
                        </a>
                    @endif
                    @if($companySettings['facebook_url'] ?? null)
                        <a href="{{ $companySettings['facebook_url'] }}" target="_blank"
                            class="px-6 py-3 bg-blue-700 text-white font-semibold rounded-full hover:bg-blue-800 transition-colors duration-300 transform hover:scale-105 inline-flex items-center">
                            <i class="fab fa-facebook-f mr-2"></i> Facebook
                        </a>
                    @endif
                    @if($companySettings['instagram_url'] ?? null)
                        <a href="{{ $companySettings['instagram_url'] }}" target="_blank"
                            class="px-6 py-3 bg-pink-600 text-white font-semibold rounded-full hover:bg-pink-700 transition-colors duration-300 transform hover:scale-105 inline-flex items-center">
                            <i class="fab fa-instagram mr-2"></i> Instagram
                        </a>
                    @endif
                </div>
                @if($companySettings['company_address'] ?? null)
                    <p class="text-gray-600 text-sm mt-6"><i
                            class="fas fa-map-marker-alt mr-2"></i>{{ $companySettings['company_address'] }}</p>
                @endif
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-800 text-white py-8 mt-auto rounded-t-lg shadow-lg">
            <div class="container mx-auto px-4 text-center">
                <p>&copy; {{ date('Y') }} Data Cerdas. Semua Hak Dilindungi.</p>
                <p class="mt-2">
                    <a href="{{ $companySettings['privacy_policy_url'] }}"
                        class="text-gray-400 hover:text-white mx-2">Kebijakan Privasi</a>
                    <a href="{{ $companySettings['terms_of_service_url'] }}"
                        class="text-gray-400 hover:text-white mx-2">Syarat & Ketentuan</a>
                </p>
            </div>
        </footer>
    </div>

    <!-- Font Awesome for icons -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Mobile menu toggle
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const closeMobileMenuButton = document.getElementById('close-mobile-menu');
            const mobileMenu = document.getElementById('mobile-menu');

            if (mobileMenuButton) {
                mobileMenuButton.addEventListener('click', function () {
                    mobileMenu.style.display = 'flex';
                });
            }

            if (closeMobileMenuButton) {
                closeMobileMenuButton.addEventListener('click', function () {
                    mobileMenu.style.display = 'none';
                });
            }
        });
    </script>
</body>

</html>