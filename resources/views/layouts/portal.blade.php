<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Portal Layanan Desa') - {{ config('app.name', 'DATA CERDAS') }}</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- CSS Kustom untuk Portal --}}
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #f4f6f9;
        }
        .content-wrapper {
            flex: 1; /* Biar area konten ngedorong footer ke bawah */
        }
        .navbar {
            background: linear-gradient(90deg, #0d7e7eff 0%, #2ededeff 100%);
        }
        .navbar-brand img {
            height: 30px;
        }
        .card-title {
            font-weight: 600;
        }
        .main-footer {
            font-size: 0.9rem;
        }
    </style>
    @stack('css')
</head>
<body>
    <div class="wrapper">

        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container">
                <a href="{{ route('portal.dashboard') }}" class="navbar-brand">
                    <img src="{{ $logo }}" alt="Logo" class="brand-image">
                    <span class="brand-text font-weight-light">Portal Desa {{ $desa->nama_desa }}</span>
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <span class="navbar-text me-3">
                                Selamat datang, {{ Auth::user()->name }}!
                            </span>
                        </li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <main class="content-wrapper py-4">
            <div class="container">
                @yield('content')
            </div>
        </main>

        <footer class="main-footer bg-white">
            <div class="container text-center">
                <strong>Copyright &copy; {{ date('Y') }} <a href="#">DATA CERDAS</a>.</strong> All rights reserved.
            </div>
        </footer>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('js')
</body>
</html>