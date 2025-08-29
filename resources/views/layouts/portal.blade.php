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
        /* CSS Kustom untuk Portal (SUDAH DIPERBAIKI) */
        body {
            background-color: #f4f6f9;
            /* Beri ruang di bawah agar konten terakhir tidak tertutup bottom nav */
            padding-bottom: 80px; 
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

        .bottom-nav {
            position: fixed; /* Kunci utamanya di sini, ia akan menempel di layar */
            bottom: 0;
            left: 0;
            right: 0;
            background-color: white;
            display: flex;
            justify-content: space-around;
            padding-top: 8px; /* Sedikit padding atas */
            padding-bottom: 8px; /* Sedikit padding bawah */
            box-shadow: 0 -2px 5px rgba(0,0,0,0.1);
            z-index: 1000;
        }

        .bottom-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            color: #6c757d;
            text-decoration: none;
            font-size: 0.75rem;
            flex-grow: 1; /* Biar tiap item lebarnya sama */
        }

        .bottom-nav-item.active {
            color: #0d7e7eff; /* Warna utama portalmu */
        }

        .bottom-nav-item i {
            font-size: 1.2rem;
            margin-bottom: 4px;
        }
        .feature-coming-soon {
            opacity: 0.6; /* Membuatnya sedikit transparan */
            cursor: not-allowed; /* Mengubah kursor menjadi tanda "dilarang" */
        }

        .feature-coming-soon a {
            pointer-events: none; /* Membuat link di dalamnya tidak bisa diklik */
        }
    </style>
    @stack('css')
</head>
<body>
    <div class="wrapper">

        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container">
                <a href="{{ route('portal.dashboard') }}" class="navbar-brand">
                    <img src="{{ asset('storage/' . $desa->path_logo) }}" alt="Logo DATA CERDAS" class="navbar-logo">
                    <span class="brand-text font-weight-light">Portal Desa {{ $desa->nama_desa }}</span>
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        {{-- Kita bungkus semua item user dalam satu <li> dengan display flex --}}
                        <li class="nav-item d-flex align-items-center">

                            {{-- 1. Teks Selamat Datang --}}
                            <span class="navbar-text me-3">
                                {{ Auth::user()->name }}
                            </span>

                            {{-- 2. Tombol Profil --}}
                            <a href="{{ route('portal.profile.edit') }}" class="btn btn-outline-light btn-sm me-2" title="Edit Profil">
                                <i class="fas fa-user-edit"></i>
                                <span class="d-none d-sm-inline ms-1">Profil</span> {{-- Teks ini hanya muncul di layar lebar --}}
                            </a>

                            {{-- 3. Tombol Logout --}}
                            <form method="POST" action="{{ route('logout') }}" class="d-flex">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm" title="Logout">
                                    <i class="fas fa-sign-out-alt"></i>
                                </button>
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

        <div class="bottom-nav d-flex d-md-none">

            {{-- MENU KHUSUS UNTUK KADER POSYANDU --}}
            @if(Auth::user()->hasRole('kader_posyandu'))

                <a href="{{ route('portal.dashboard') }}"
                class="bottom-nav-item {{ request()->routeIs('portal.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    <span>Beranda</span>
                </a>
                {{-- 1. Tombol Dashboard/Sesi Posyandu --}}
                <a href="{{ route('portal.posyandu.index', ['subdomain' => app('tenant')->subdomain]) }}"
                class="bottom-nav-item {{ request()->routeIs('portal.posyandu.index', 'portal.posyandu.sesi.show') ? 'active' : '' }}">
                    <i class="fas fa-tasks"></i>
                    <span>Sesi</span>
                </a>

                {{-- 2. Tombol Laporan --}}
                <a href="{{ route('portal.posyandu.laporan.index', ['subdomain' => app('tenant')->subdomain]) }}"
                class="bottom-nav-item {{ request()->routeIs('portal.posyandu.laporan.*') ? 'active' : '' }}">
                    <i class="fas fa-file-alt"></i>
                    <span>Laporan</span>
                </a>

                {{-- 3. Tombol Rekam Medis --}}
                <a href="{{ route('portal.posyandu.rekam_medis.search', ['subdomain' => app('tenant')->subdomain]) }}"
                class="bottom-nav-item {{ request()->routeIs('portal.posyandu.rekam_medis.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    <span>Rekam Medis</span>
                </a>

            {{-- MENU UNTUK PERAN LAIN (RT, RW, DLL) --}}
            @else

                {{-- 1. Tombol Beranda --}}
                <a href="{{ route('portal.dashboard') }}"
                class="bottom-nav-item {{ request()->routeIs('portal.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    <span>Beranda</span>
                </a>

                {{-- 2. Tombol Warga --}}
                <a href="{{ route('portal.warga.index') }}"
                class="bottom-nav-item {{ request()->routeIs('portal.warga.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span>Warga</span>
                </a>

                {{-- 3. Tombol Surat --}}
                <a href="{{ route('portal.surat.index') }}"
                class="bottom-nav-item {{ request()->routeIs('portal.surat.*') ? 'active' : '' }}">
                    <i class="fas fa-envelope"></i>
                    <span>Surat</span>
                </a>

                {{-- 4. Tombol Bantuan --}}
                <a href="{{ route('portal.bantuan.pilihBantuan') }}"
                class="bottom-nav-item {{ request()->routeIs('portal.bantuan.*') ? 'active' : '' }}">
                    <i class="fas fa-hand-holding-dollar"></i>
                    <span>Bantuan</span>
                </a>

            @endif

        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('js')
</body>
</html>