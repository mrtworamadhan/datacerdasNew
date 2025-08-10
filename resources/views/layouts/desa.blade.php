<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Desa Cerdas - Desa {{ $desa->nama_desa }}</title>
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


        .card-shadow {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08);
        }

        .navbar-logo {
            height: 40px;
            width: auto;
            margin-right: 12px;
        }

        .navbar-logoFont {
            height: 30px;
            width: auto;
            margin-right: 12px;
        }
    </style>
    @stack('css')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
</head>

<body class="antialiased">
    <div class="mobile-container">
        <!-- Navbar -->
        <nav class="bg-white shadow-md p-4 flex justify-between items-center fixed w-full z-10 rounded-b-lg">
            <div class="flex items-center">
                <a href="/" class="flex items-center">
                    <img src="{{ asset('storage/' . $desa->path_logo) }}" alt="Logo DATA CERDAS" class="navbar-logo">
                    Desa {{ $desa->nama_desa }}
                </a>
            </div>
            <div>
                @if (Route::has('login'))
                    <div class="space-x-4 hidden md:flex"> {{-- Sembunyikan di mobile, tampilkan di desktop --}}
                        <a href="{{ route('about.us') }}"
                            class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors duration-300">
                            Tentang Desa</a>
                        <a href="{{ route('public.desas.index') }}"
                            class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors duration-300">
                            Desa Cerdas</a>
                        <a href="{{ route('public.fasum.index') }}"
                            class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors duration-300">
                            Fasum Cerdas</a>
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
            <div class="container">
                @yield('content')
            </div>
        </main>
    </div>
    <!-- Footer -->
    <footer class="bg-gray-100 py-12 text-gray-700 text-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Grid Utama -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">

                <!-- Kolom 1: Tentang Desa -->
                <div>
                    <h5 class="text-lg font-semibold mb-2">Tentang {{ $desa->nama_desa ?? 'Desa Cerdas' }}</h5>
                    <p class="mb-2">{{ $desa->alamat_desa ?? 'Alamat lengkap desa.' }}</p>
                    <p>Email: info@desa.id | Telp: (0251) 123-456</p>
                </div>

                <!-- Kolom 2: Navigasi -->
                <div>
                    <h5 class="text-lg font-semibold mb-2">Navigasi</h5>
                    <ul class="space-y-1">
                        <li><a href="{{ route('welcome') }}" class="hover:underline">Beranda</a></li>
                        <li><a href="#" class="hover:underline">Profil Desa</a></li>
                        <li><a href="#" class="hover:underline">Berita</a></li>
                        <li><a href="#" class="hover:underline">Layanan</a></li>
                    </ul>
                </div>

                <!-- Kolom 3: Layanan -->
                <div>
                    <h5 class="text-lg font-semibold mb-2">Layanan Publik</h5>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('anjungan.index', ['subdomain' => $desa->subdomain ?? '']) }}"
                                class="hover:underline">Anjungan Mandiri</a>
                        </li>
                        <li><a href="#" class="hover:underline">Laporan Keuangan</a></li>
                        <li><a href="#" class="hover:underline">Data Kependudukan</a></li>
                    </ul>
                </div>

                <!-- Kolom 4: Logo -->
                <div class="flex flex-col items-center lg:items-end">
                    <img src="{{ asset('images/logo/logo colour trp.png') }}" alt="Logo DATA CERDAS"
                        class="h-12 object-contain">
                    <p class="mt-2">Platform Digitalisasi Desa</p>
                </div>
            </div>

            <!-- Copyright -->
            <div class="mt-8 pt-4 text-center text-xs text-black">
                &copy; {{ date('Y') }} {{ $desa->nama_desa ?? 'Desa Cerdas' }}. Didukung oleh DATA CERDAS.
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    @stack('js')
</body>

</html>