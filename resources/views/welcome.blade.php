@extends('layouts.public')

@section('content')
    <section class="hero-section">
        <div class="container mx-auto px-4">
            <div class="flex flex-col lg:flex-row items-center">

                {{-- KOLOM KIRI --}}
                <div class="lg:w-1/2 text-white text-center lg:text-left">
                    <h1 class="text-5xl font-bold leading-tight mb-4">
                        Aplikasi Manajemen Desa<br>Cerdas & Terintegrasi
                    </h1>
                    <p class="text-xl mb-6">
                        Satu platform untuk semua kebutuhan administrasi desa, dari data warga, kesehatan, aset, hingga
                        pelaporan keuangan otomatis.
                    </p>

                    <div class="flex flex-wrap justify-center lg:justify-start mb-6">
                        <span class="feature-capsule blue">Administrasi Cerdas</span>
                        <span class="feature-capsule green">Lembaga Cerdas</span>
                        <span class="feature-capsule purple">Aset Cerdas</span>
                        <span class="feature-capsule red">Fasum Cerdas</span>
                        <span class="feature-capsule orange">Data Warga Cerdas</span>
                        <span class="feature-capsule blue">Pelayanan Cerdas</span>
                        <span class="feature-capsule purple">Posyandu Cerdas</span>
                        <span class="feature-capsule default">Anjungan Cerdas</span>
                    </div>

                    <div>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $companySettings['whatsapp_number']) }}"
                            target="_blank"
                            class="bg-primary text-white text-lg px-3 py-2 rounded-lg mr-2 hover:bg-primary-700 transition">
                            Daftarkan Desa Anda
                        </a>
                        <a href="{{ route('login') }}"
                            class="border border-white text-white text-lg px-3 py-2 rounded-lg hover:bg-white hover:text-primary transition">
                            Login
                        </a>
                    </div>
                </div>

                {{-- KOLOM KANAN --}}
                <div class="lg:w-1/2 mt-8 lg:mt-0">
                    <div class="image-card-wrapper" x-data="{
                            activeSlide: 0,
                            slides: [
                                '{{ asset('images/welcome/12.png') }}',
                                '{{ asset('images/welcome/910.png') }}',
                                '{{ asset('images/welcome/56.png') }}'
                            ],
                            init() {
                                setInterval(() => {
                                    this.activeSlide = (this.activeSlide + 1) % this.slides.length;
                                }, 3000);
                            }
                        }">
                        <div class="relative w-full overflow-hidden rounded-xl shadow">
                            <div class="flex transition-transform duration-700"
                                :style="`transform: translateX(-${activeSlide * 100}%)`">
                                <template x-for="(slide, index) in slides" :key="index">
                                    <div class="w-full flex-shrink-0">
                                        <img :src="slide" class="w-full h-[500px] object-cover hero-image">
                                    </div>
                                </template>
                            </div>
                            <!-- Navigasi Optional
                            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex space-x-2">
                                <template x-for="(slide, index) in slides" :key="index">
                                    <div @click="activeSlide = index"
                                        class="w-3 h-3 rounded-full cursor-pointer"
                                        :class="{
                                            'bg-white': activeSlide !== index,
                                            'bg-[#254B77]': activeSlide === index
                                        }"
                                    ></div>
                                </template>
                            </div> -->
                            <div class="hero-card-overlay absolute bottom-0 left-0 w-full bg-white/80 rounded-b-xl">
                                <div class="p-4">
                                    <h5 class="text-lg font-bold text-gray-800">DATA CERDAS</h5>
                                    <p class="text-sm text-gray-700">
                                        Platform digital modern untuk memberdayakan pemerintah desa dan meningkatkan
                                        pelayanan kepada masyarakat secara efisien, transparan, dan cerdas.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
    @include('public.partials._features_section')
    @include('public.partials._testimony_section')
    @include('public.partials._solutions_section')
@endsection
@push('css')
    <style>
        .hero-section {
            background: linear-gradient(90deg, #0d7e7eff 0%, #46d7d7ff 100%);
            padding: 6rem 0;
            overflow: hidden;
        }

        .feature-capsule {
            padding: 0.4rem 0.9rem;
            border-radius: 20px;
            font-size: 0.9rem;
            margin-right: 10px;
            margin-bottom: 10px;
            display: inline-block;
            color: #fff;
        }

        /* Warna default */
        .feature-capsule.default {
            background-color: rgba(255, 255, 255, 0.48);
            color: #333;
        }

        /* Warna-warna tambahan */
        .feature-capsule.blue {
            background-color: #3B82F6;
        }

        .feature-capsule.green {
            background-color: #10B981;
        }

        .feature-capsule.purple {
            background-color: #8B5CF6;
        }

        .feature-capsule.red {
            background-color: #EF4444;
        }

        .feature-capsule.orange {
            background-color: #F97316;
        }

        .image-card-wrapper {
            position: relative;
        }

        .hero-image {
            border-radius: 15px;
            height: 450px;
            object-fit: cover;
        }

        .hero-card-overlay {
            position: absolute;
            bottom: -20px;
            left: 5%;
            right: 5%;
            background-color: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            color: #333;
        }

        @media (max-width: 991.98px) {
            .hero-section {
                padding: 3rem 0;
            }

            .hero-card-overlay {
                position: relative;
                bottom: 0;
                left: 0;
                right: 0;
                margin-top: -50px;
                margin-left: 15px;
                margin-right: 15px;
            }
        }

        .features-section {
            background-color: #F8F9FA;
            padding: 5rem 0;
        }

        .section-title {
            font-weight: 700;
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .section-subtitle {
            font-size: 1.1rem;
            color: #6c757d;
        }

        .feature-card {
            background-color: #fff;
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.07);
            overflow: hidden;
            /* Agar gambar mengikuti border-radius */
            height: 100%;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        }

        .feature-card .card-img-top {
            width: 100%;
            position: relative;
        }

        .feature-card .card-img-top img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .feature-card .card-body {
            padding: 2rem;
        }

        .feature-card .feature-capsules {
            margin-bottom: 1rem;
        }

        .feature-card .capsule {
            background-color: #E9ECEF;
            color: #495057;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
            margin-right: 5px;
            margin-bottom: 5px;
        }

        .feature-card .card-title {
            font-weight: 600;
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
        }

        .feature-card .card-text {
            color: #6c757d;
        }

        .testimony-section {
            background-color: #F6FAF2;
            padding: 4rem 0;
        }

        .testimony-holder {
            background: linear-gradient(135deg, #28a745, #20c997);
            border-radius: 20px;
            padding: 3rem;
            color: white;
        }

        .video-player {
            position: relative;
            cursor: pointer;
        }

        .video-thumbnail {
            border-radius: 15px;
        }

        .video-play-button {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80px;
            transition: transform 0.2s ease;
        }

        .video-player:hover .video-play-button {
            transform: translate(-50%, -50%) scale(1.1);
        }

        .testimony-caption {
            padding-left: 2rem;
        }

        .caption-label {
            font-weight: 600;
        }

        .caption-title {
            font-size: 2.2rem;
            font-weight: 700;
            margin: 0.5rem 0 1rem 0;
        }

        .caption-link {
            color: white;
            text-decoration: underline;
        }

        .solutions-section {
            padding: 5rem 0;
        }

        .nav-pills .nav-link {
            color: #333;
        }

        .nav-pills .nav-link.active {
            background-color: #28a745;
            color: white;
        }

        .tab-content {
            padding: 2rem 0;
        }

        .solution-title {
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .solution-features {
            list-style: none;
            padding: 0;
        }

        .solution-features li {
            margin-bottom: 0.5rem;
        }

        .solution-features .fa-check-circle {
            color: #28a745;
            margin-right: 10px;
        }
    </style>
@endpush
@push('js')
    <script>
        $('#videoModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var videoID = button.data('video-id');
            var videoURL = 'https://www.youtube.com/embed/' + videoID + '?autoplay=1';
            $('#video').attr('src', videoURL);
        });
        $('#videoModal').on('hide.bs.modal', function () {
            $('#video').attr('src', '');
        });
    </script>
@endpush