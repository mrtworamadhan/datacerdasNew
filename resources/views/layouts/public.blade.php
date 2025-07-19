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
            background-image: url('{{ asset(' images/welcome/bg-hero.png') }}');
            background-size: cover;
            background-position: center;
            position: relative;
            min-height: 450px;

        }

        .overlay {
            background-color: rgba(41, 41, 41, 0.67);
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 1rem;
            /* default padding kecil */
            box-sizing: border-box;
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
            color: white;
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
    </style>
</head>

<body class="antialiased">
    <div class="mobile-container">
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
                            class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors duration-300">Ketentuan
                            Layanan</a>
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
                class="text-xl font-medium text-gray-800 hover:text-primary transition-colors duration-300">Ketentuan
                Layanan</a>
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
    @stack('js')
</body>

</html>