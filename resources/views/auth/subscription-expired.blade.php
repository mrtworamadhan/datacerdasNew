<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Langganan Berakhir - Data Cerdas</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }

        .expired-bg {
            background-image: url('{{ asset('images/welcome/bg.png') }}');
            /* Placeholder background */
            background-size: cover;
            background-position: center;
        }

        .card-shadow {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08);
        }
    </style>
</head>

<body class="antialiased">
    <div class="min-h-screen flex flex-col justify-center items-center expired-bg">
        <div class="bg-white p-8 rounded-xl card-shadow text-center max-w-lg mx-auto">
            <img src="{{ asset('images/logo/logo only trp.png') }}" alt="Logo Desa Cerdas"
                class="h-20 w-auto mx-auto mb-4">
            <h1 class="text-3xl font-bold text-red-600 mb-4">Langganan Desa Anda Telah Berakhir!</h1>
            <p class="text-gray-700 mb-6">
                Akses Anda ke fitur-fitur Data Cerdas telah dibatasi karena masa langganan desa Anda sudah habis.
                Untuk melanjutkan penggunaan aplikasi, harap segera perpanjang langganan.
            </p>
            <p class="text-gray-600 text-sm mb-6">
                Silakan hubungi Super Admin platform Data Cerdas untuk informasi lebih lanjut mengenai perpanjangan
                langganan.
            </p>
            <div class="flex flex-col sm:flex-row justify-center items-center gap-4 mt-6">
                <a href="https://wa.me/6281277761133?text=Halo..." target="_blank"
                    class="inline-flex justify-center items-center w-full sm:w-auto px-6 py-3 bg-green-500 text-white font-semibold rounded-full hover:bg-green-600 transition-colors duration-300">
                    Perpanjang Langganan
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="block w-full sm:w-auto">
                    @csrf
                    <button type="submit"
                        class="inline-flex justify-center items-center w-full sm:w-auto px-6 py-3 bg-gray-500 text-white font-semibold rounded-full hover:bg-gray-600 transition-colors duration-300">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>