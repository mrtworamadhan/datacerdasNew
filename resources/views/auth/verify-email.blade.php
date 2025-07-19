<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Verifikasi Email - Desa Cerdas</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
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
                            DEFAULT: '#10A8A8',
                            '50': '#E0F2F2', '100': '#B3E0E0', '200': '#80CCCC', '300': '#4DB8B8', '400': '#26A3A3',
                            '500': '#10A8A8', '600': '#0C8A8A', '700': '#086C6C', '800': '#044D4D', '900': '#022E2E', '950': '#011717',
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
            background-color: #f8f9fa; /* Light gray background */
        }
        .auth-bg {
            background-image: url('{{ asset('images/welcome/bg.png') }}'); /* Placeholder image */
            background-size: cover;
            background-position: center;
        }
        .card-shadow {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08);
        }
        .form-input {
            border-radius: 0.5rem; /* Rounded corners */
            border: 1px solid #D1D5DB; /* Light gray border */
            padding: 0.75rem 1rem; /* Padding */
            width: 100%;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        .form-input:focus {
            border-color: #10A8A8; /* Primary color on focus */
            box-shadow: 0 0 0 3px rgba(16, 168, 168, 0.25); /* Light shadow on focus */
            outline: none;
        }
    </style>
</head>
<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 auth-bg">
        <div>
            <a href="/" class="flex flex-col items-center">
                <img src="{{ asset('images/logo/logo only trp.png') }}" alt="Logo Desa Cerdas" class="h-20 w-auto fill-current text-gray-500 mb-2">
                <img src="{{ asset('images/logo/logo line.png') }}" alt="LogoFont Desa Cerdas" class="h-10 w-auto fill-current text-gray-500">
            </a>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white card-shadow overflow-hidden sm:rounded-lg rounded-xl">
            <div class="mb-4 text-sm text-gray-600">
                {{ __('Terima kasih telah mendaftar! Sebelum memulai, bisakah Anda memverifikasi alamat email Anda dengan mengklik tautan yang baru saja kami kirimkan kepada Anda? Jika Anda tidak menerima email tersebut, kami dengan senang hati akan mengirimkan yang lain.') }}
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="mb-4 font-medium text-sm text-green-600">
                    {{ __('Tautan verifikasi baru telah dikirim ke alamat email yang Anda berikan saat pendaftaran.') }}
                </div>
            @endif

            <div class="mt-4 flex items-center justify-between">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf

                    <div>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-600 focus:bg-primary-600 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition ease-in-out duration-150">
                            Kirim Ulang Email Verifikasi
                        </button>
                    </div>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary ml-4">
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
