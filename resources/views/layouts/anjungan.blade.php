<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Anjungan Mandiri Desa</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    
    {{-- ====================================================== --}}
    {{-- === CSS BARU DENGAN PENDEKATAN LANDSCAPE-FIRST === --}}
    {{-- ====================================================== --}}
    <style>
        body.login-page {
            background-image: linear-gradient(to right top, #051937, #004d7a, #008793, #00bf72, #a8eb12);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Aturan untuk layar besar (lebar 992px ke atas) */
        .login-box {
            width: 800px; /* Lebarkan kotaknya untuk layar landscape */
            max-width: 95%;
        }
        .login-logo {
            font-size: 2.5rem; /* Perbesar font logo */
        }
        .login-box-msg {
            font-size: 1.3rem; /* Perbesar font petunjuk */
        }
        .btn-block {
             padding: 1rem; /* Perbesar tombol */
             font-size: 1.5rem;
        }

        /* Aturan untuk layar kecil (di bawah 992px) - Tetap Responsif */
        @media (max-width: 991.98px) {
            .login-box {
                width: 450px; /* Kembali ke ukuran mobile */
            }
             .login-logo {
                font-size: 2.1rem;
            }
        }
    </style>
    @stack('css')
</head>
<body class="hold-transition login-page">
    <div class="login-box">
      <div class="login-logo">
        @php
            $desa = app('tenant');
            $suratSetting = $desa ? \App\Models\SuratSetting::where('desa_id', $desa->id)->first() : null;
        @endphp

        @if($suratSetting && $suratSetting->path_logo_pemerintah && file_exists(public_path('storage/' . $suratSetting->path_logo_pemerintah)))
            <img src="{{ asset('storage/' . $suratSetting->path_logo_pemerintah) }}" alt="Logo" style="max-width: 100px; margin-bottom: 1rem;">
        @endif
        <br>
        <a href="{{ route('anjungan.index', ['subdomain' => $desa?->subdomain ?? '']) }}">
            <b>Anjungan Mandiri</b><br>
            Desa {{ $desa?->nama_desa ?? '[NAMA DESA]' }}
        </a>
      </div>
      <div class="card">
        <div class="card-body login-card-body" style="border-radius: 15px;">
          @yield('content')
        </div>
      </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('js')
</body>
</html>