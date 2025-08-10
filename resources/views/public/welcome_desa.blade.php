@extends('layouts.desa') {{-- Menggunakan layout yang sama dengan welcome utama --}}

@section('title', 'Selamat Datang di Desa' . $desa->nama_desa)

@section('content')
    <!-- <section class="hero-section">
                                                <div class="container">
                                                    <div class="row align-items-center">

                                                        {{-- Kolom Kiri: Teks & Statistik Desa --}}
                                                        <div class="col-lg-6 text-white text-center text-lg-left">
                                                            <h1 class="display-4 font-weight-bold">
                                                                Selamat Datang di<br>{{ $desa->nama_desa }}
                                                            </h1>
                                                            <p class="lead my-4">
                                                                Situs informasi publik resmi {{ $desa->nama_desa }}. Temukan data, layanan, dan informasi terkini
                                                                tentang desa kami.
                                                            </p>

                                                            {{-- Statistik Publik Desa --}}
                                                            <div class="row text-center text-lg-left">
                                                                <div class="col-6 col-md-4 mb-3">
                                                                    <div class="stat-item">
                                                                        <h3 class="font-weight-bold">{{ $jumlahWarga }}</h3>
                                                                        <p>Jumlah Warga</p>
                                                                    </div>
                                                                </div>
                                                                <div class="col-6 col-md-4 mb-3">
                                                                    <div class="stat-item">
                                                                        <h3 class="font-weight-bold">{{ $jumlahKk }}</h3>
                                                                        <p>Jumlah Keluarga</p>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            {{-- Tombol Aksi --}}
                                                            <div class="mt-4">
                                                                <a href="{{ route('anjungan.index', ['subdomain' => $desa->subdomain]) }}"
                                                                    class="btn btn-light btn-lg px-4 mr-2">Layanan Mandiri</a>
                                                                <a href="{{ route('login', ['subdomain' => $desa->subdomain]) }}"
                                                                    class="btn btn-outline-light btn-lg px-4">Login Staff</a>
                                                            </div>
                                                        </div>

                                                        {{-- Kolom Kanan: Gambar Desa --}}
                                                        <div class="col-lg-6 mt-5 mt-lg-0 text-center">
                                                            {{-- Ganti dengan path foto desa jika ada --}}
                                                            <img src="{{ asset('images/welcome/78.png') }}" class="img-fluid" alt="Ilustrasi Desa">
                                                        </div>

                                                    </div>
                                                </div>
                                            </section> -->
    <section class="hero-section">
        <div class="container mx-auto px-4">
            <div class="flex flex-col lg:flex-row items-center">

                {{-- KOLOM KIRI --}}
                <div class="lg:w-1/2 text-white text-center lg:text-left">
                    <h1 class="text-5xl font-bold leading-tight mb-4">
                        Selamat Datang di<br>{{ $desa->nama_desa }}
                    </h1>
                    <p class="text-xl mb-6">
                        Situs informasi publik resmi {{ $desa->nama_desa }}. Temukan data, layanan, dan informasi terkini
                        tentang desa kami.
                    </p>

                    <!-- <div class="flex flex-wrap justify-center lg:justify-start mb-6">
                                                                <span class="feature-capsule blue">Administrasi Cerdas</span>
                                                                <span class="feature-capsule green">Lembaga Cerdas</span>
                                                                <span class="feature-capsule purple">Aset Cerdas</span>
                                                                <span class="feature-capsule red">Fasum Cerdas</span>
                                                                <span class="feature-capsule orange">Data Warga Cerdas</span>
                                                                <span class="feature-capsule blue">Pelayanan Cerdas</span>
                                                                <span class="feature-capsule purple">Posyandu Cerdas</span>
                                                                <span class="feature-capsule default">Anjungan Cerdas</span>
                                                            </div> -->

                    <div>
                        <a href="{{ route('anjungan.index', ['subdomain' => $desa->subdomain]) }}" target="_blank"
                            class="bg-primary text-white text-lg px-3 py-2 rounded-lg mr-2 hover:bg-primary-700 transition">
                            Anjungan Cerdas
                        </a>
                        <a href="{{ route('login', ['subdomain' => $desa->subdomain]) }}"
                            class="border border-white text-white text-lg px-3 py-2 rounded-lg hover:bg-white hover:text-primary transition">
                            Login Staff
                        </a>
                    </div>
                </div>

                {{-- KOLOM KANAN --}}
                <div class="lg:w-1/2 mt-8 lg:mt-0">
                    <div class="relative w-full overflow-hidden rounded-xl shadow">
                        <img src="{{ asset('storage/' . $desa->foto_kades_path) }}" alt="Foto Kepala Desa"
                            class="w-full h-[500px] object-cover hero-image">

                        <div class="hero-card-overlay absolute bottom-0 left-0 w-full bg-white/80 rounded-b-xl">
                            <div class="p-4">
                                <h5 class="text-lg font-bold text-gray-800">Kepala {{ $desa->nama_desa }} - {{ $desa->nama_kades }}</h5>
                                <p class="text-sm text-gray-700">
                                    "Tidak ada kemajuan tanpa perubahan — digitalisasi adalah langkah nyata
                                    Desa kita menuju pelayanan yang cepat, transparan, dan bermartabat."
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
    {{-- Di sini nanti kita bisa tambahkan section lain seperti Berita Desa, Daftar Fasum, dll. --}}
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-12 text-center">
                <h2 class="text-3xl font-bold text-gray-800">Statistik Warga</h2>
                <p class="mt-2 text-gray-600">Temukan berbagai statistik terkait warga desa.</p>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">

                <!-- Card: Total Penduduk -->
                <div class="bg-white shadow-md rounded-lg p-6">
                    <h2 class="text-3xl font-bold text-blue-600">{{ $stats['jumlah_warga'] }}</h2>
                    <p class="mt-2 text-sm text-gray-600">Total Penduduk</p>
                </div>

                <!-- Card: Total Keluarga -->
                <div class="bg-white shadow-md rounded-lg p-6">
                    <h2 class="text-3xl font-bold text-green-600">{{ $stats['jumlah_kk'] }}</h2>
                    <p class="mt-2 text-sm text-gray-600">Total Keluarga</p>
                </div>

                <!-- Card: Jumlah RW -->
                <div class="bg-white shadow-md rounded-lg p-6">
                    <h2 class="text-3xl font-bold text-purple-600">{{ $stats['jumlah_rw'] }}</h2>
                    <p class="mt-2 text-sm text-gray-600">Jumlah RW</p>
                </div>

                <!-- Card: Jumlah RT -->
                <div class="bg-white shadow-md rounded-lg p-6">
                    <h2 class="text-3xl font-bold text-rose-600">{{ $stats['jumlah_rt'] }}</h2>
                    <p class="mt-2 text-sm text-gray-600">Jumlah RT</p>
                </div>

            </div>
        </div>
    </section>
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Judul -->
            <div class="mb-12 text-center">
                <h2 class="text-3xl font-bold text-gray-800">Fasilitas Umum Desa</h2>
                <p class="mt-2 text-gray-600">Temukan berbagai fasilitas publik yang tersedia untuk menunjang kegiatan
                    masyarakat.</p>
            </div>

            <!-- Grid Fasilitas -->
            <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                @forelse($fasums as $fasum)
                    <div class="bg-white rounded-xl shadow hover:shadow-md transition overflow-hidden">
                        <img src="{{ $fasum->photos->first() ? asset('storage/' . $fasum->photos->first()->path) : 'https://placehold.co/600x400' }}"
                            alt="{{ $fasum->nama_fasum }}" class="w-full h-48 object-cover">
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-800">{{ $fasum->nama_fasum }}</h3>
                            <p class="mt-2 text-sm text-gray-600">{{ Str::limit($fasum->deskripsi, 100) }}</p>
                        </div>
                    </div>
                @empty
                    <p class="col-span-full text-center text-gray-500">Data fasilitas umum belum diinput.</p>
                @endforelse
            </div>
        </div>
    </section>
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Judul -->
            <div class="mb-12 text-center">
                <h2 class="text-3xl font-bold text-gray-800">Aparatur Pemerintahan Desa</h2>
                <p class="mt-2 text-gray-600">Tim kami yang berdedikasi untuk melayani seluruh warga desa.</p>
            </div>

            <!-- Swiper Wrapper (Mobile) -->
            <div class="block md:hidden">
                <div class="swiper-perangkat">
                    <div class="swiper-wrapper">
                        @forelse($perangkatDesa as $perangkat)
                            <div class="swiper-slide">
                                <div class="bg-white rounded-xl shadow-md overflow-hidden w-[250px] mx-auto text-center">
                                    <img src="{{ $perangkat->foto_path ? asset('storage/' . $perangkat->foto_path) : 'https://placehold.co/300x300?text=Foto' }}"
                                        alt="{{ $perangkat->nama }}" class="w-full h-60 object-cover">
                                    <div class="p-4">
                                        <h5 class="text-lg font-semibold text-gray-800">{{ $perangkat->nama }}</h5>
                                        <p class="text-sm text-gray-500">{{ $perangkat->jabatan }}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-gray-500">Data perangkat desa belum diinput.</p>
                        @endforelse
                    </div>
                    <!-- Optional navigation -->
                    <div class="swiper-pagination mt-4"></div>
                </div>
            </div>

            <!-- Grid View (Desktop) -->
            <div class="hidden md:grid gap-8 justify-center grid-cols-2 lg:grid-cols-4">
                @forelse($perangkatDesa as $perangkat)
                    <div class="bg-white rounded-xl shadow-md overflow-hidden w-full max-w-xs text-center">
                        <img src="{{ $perangkat->foto_path ? asset('storage/' . $perangkat->foto_path) : 'https://placehold.co/300x300?text=Foto' }}"
                            alt="{{ $perangkat->nama }}" class="w-full h-60 object-cover">
                        <div class="p-4">
                            <h5 class="text-lg font-semibold text-gray-800">{{ $perangkat->nama }}</h5>
                            <p class="text-sm text-gray-500">{{ $perangkat->jabatan }}</p>
                        </div>
                    </div>
                @empty
                    <p class="col-span-full text-center text-gray-500">Data perangkat desa belum diinput.</p>
                @endforelse
            </div>

        </div>
    </section>

    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Judul -->
            <div class="mb-12 text-center">
                <h2 class="text-3xl font-bold text-gray-800">Lembaga & Kelompok Desa</h2>
                <p class="mt-2 text-gray-600">
                    Organisasi kemasyarakatan yang aktif berperan dalam pembangunan desa.
                </p>
            </div>

            <!-- Carousel (Mobile) -->
            <div class="block md:hidden">
                <div class="swiper-mitra">
                    <div class="swiper-wrapper">
                        @forelse($mitraDesa as $mitra)
                            <div class="swiper-slide flex justify-center">
                                <div class="flex flex-col items-center text-center space-y-2 w-[120px]">
                                    <div
                                        class="bg-white p-4 rounded-lg shadow-sm w-full h-[100px] flex items-center justify-center">
                                        <img src="{{ asset('storage/' . $mitra->path_kop_surat) }}"
                                            alt="{{ $mitra->nama_lembaga ?? $mitra->nama_kelompok }}"
                                            class="object-contain h-full w-full">
                                    </div>
                                    <p class="text-sm text-gray-600 font-medium leading-tight">
                                        {{ $mitra->nama_lembaga ?? $mitra->nama_kelompok }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-gray-500">Belum ada data lembaga atau kelompok.</p>
                        @endforelse
                    </div>
                    <div class="swiper-pagination mt-4"></div>
                </div>
            </div>

            <!-- Grid (Desktop) -->
            <div class="hidden md:grid gap-6 grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 justify-items-center">
                @forelse($mitraDesa as $mitra)
                    <div class="flex flex-col items-center text-center space-y-2 w-full max-w-[140px]">
                        <div class="bg-white p-4 rounded-lg shadow-sm w-full h-[120px] flex items-center justify-center">
                            <img src="{{ asset('storage/' . $mitra->path_kop_surat) }}"
                                alt="{{ $mitra->nama_lembaga ?? $mitra->nama_kelompok }}" class="object-contain h-full w-full">
                        </div>
                        <p class="text-sm text-gray-600 font-medium leading-tight">
                            {{ $mitra->nama_lembaga ?? $mitra->nama_kelompok }}
                        </p>
                    </div>
                @empty
                    <p class="col-span-full text-center text-gray-500">
                        Belum ada data lembaga atau kelompok.
                    </p>
                @endforelse
            </div>

        </div>
    </section>
@endsection

@push('css')
    {{-- Kita bisa tambahkan sedikit CSS kustom jika perlu --}}
    <style>
        .hero-section {
            background: linear-gradient(90deg, #0d7e7eff 0%, #46d7d7ff 100%);
            padding: 6rem 0;
            overflow: hidden;
        }

        .stat-item h3 {
            margin-bottom: 0;
        }

        .stat-item p {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        .stats-section {
            padding: 4rem 0;
            background-color: #f8f9fa;
        }

        .stats-section .stat-item p {
            color: #6c757d;
        }
    </style>
@endpush
@push('js')
    <script>
        new Swiper('.swiper-perangkat', {
            slidesPerView: 1.5,
            spaceBetween: 16,
            pagination: {
                el: '.perangkat-pagination',
                clickable: true,
            },
        });

        new Swiper('.swiper-mitra', {
            slidesPerView: 2,
            spaceBetween: 16,
            pagination: {
                el: '.mitra-pagination',
                clickable: true,
            },
        });
    </script>
@endpush