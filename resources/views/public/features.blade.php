@extends('layouts.public')

@section('title', 'Fitur Kami - DATA CERDAS')

@section('content')
<section class="bg-gradient-to-r from-blue-600 to-cyan-700 text-white py-20">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-5xl font-extrabold mb-4 animate-fade-in-down">Fitur Unggulan DATA CERDAS</h1>
        <p class="text-xl opacity-90 animate-fade-in-up">
            Solusi Lengkap untuk Tata Kelola Desa yang Efisien dan Modern.
        </p>
    </div>
</section>

<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <h2 class="text-4xl font-bold text-gray-800 text-center mb-12 animate-fade-in-down">Apa yang Kami Tawarkan?</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

            <!-- Fitur 1: Manajemen Kependudukan -->
            <div class="bg-white p-8 rounded-lg shadow-lg transform hover:scale-105 transition-transform duration-300 animate-fade-in-up">
                <div class="text-5xl text-purple-600 mb-4 text-center">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="text-2xl font-semibold text-gray-800 mb-3 text-center">Manajemen Kependudukan</h3>
                <p class="text-gray-600 text-center">
                    Kelola data warga, Kartu Keluarga (KK), dan anggota keluarga dengan mudah. Dilengkapi fitur OCR KTP untuk input data yang cepat dan akurat.
                </p>
            </div>

            <!-- Fitur 2: Manajemen Bantuan Sosial -->
            <div class="bg-white p-8 rounded-lg shadow-lg transform hover:scale-105 transition-transform duration-300 animate-fade-in-up delay-100">
                <div class="text-5xl text-purple-600 mb-4 text-center">
                    <i class="fas fa-hand-holding-heart"></i>
                </div>
                <h3 class="text-2xl font-semibold text-gray-800 mb-3 text-center">Manajemen Bantuan Sosial</h3>
                <p class="text-gray-600 text-center">
                    Sistem pengajuan dan verifikasi bantuan sosial berjenjang, memastikan penyaluran yang tepat sasaran. Export data dalam format PDF dan Excel.
                </p>
            </div>

            <!-- Fitur 3: Manajemen Surat & Perizinan -->
            <div class="bg-white p-8 rounded-lg shadow-lg transform hover:scale-105 transition-transform duration-300 animate-fade-in-up delay-200">
                <div class="text-5xl text-purple-600 mb-4 text-center">
                    <i class="fas fa-file-alt"></i>
                </div>
                <h3 class="text-2xl font-semibold text-gray-800 mb-3 text-center">Manajemen Surat & Perizinan</h3>
                <p class="text-gray-600 text-center">
                    Pembuatan dan pengajuan berbagai jenis surat desa secara digital, dengan alur persetujuan yang efisien dan cetak PDF otomatis.
                </p>
            </div>

            <!-- Fitur 4: Data Kesehatan Anak -->
            <div class="bg-white p-8 rounded-lg shadow-lg transform hover:scale-105 transition-transform duration-300 animate-fade-in-up delay-300">
                <div class="text-5xl text-purple-600 mb-4 text-center">
                    <i class="fas fa-child"></i>
                </div>
                <h3 class="text-2xl font-semibold text-gray-800 mb-3 text-center">Data Kesehatan Anak</h3>
                <p class="text-gray-600 text-center">
                    Catat dan pantau data kesehatan anak di desa, membantu program posyandu dan kesehatan masyarakat.
                </p>
            </div>

            <!-- Fitur 5: Manajemen Fasilitas Umum -->
            <div class="bg-white p-8 rounded-lg shadow-lg transform hover:scale-105 transition-transform duration-300 animate-fade-in-up delay-400">
                <div class="text-5xl text-purple-600 mb-4 text-center">
                    <i class="fas fa-building"></i>
                </div>
                <h3 class="text-2xl font-semibold text-gray-800 mb-3 text-center">Manajemen Fasilitas Umum</h3>
                <p class="text-gray-600 text-center">
                    Inventarisasi dan pengelolaan data fasilitas umum desa, termasuk detail lokasi, ukuran, dan kondisi.
                </p>
            </div>

            <!-- Fitur 6: Laporan Kegiatan & Anggaran -->
            <div class="bg-white p-8 rounded-lg shadow-lg transform hover:scale-105 transition-transform duration-300 animate-fade-in-up delay-500">
                <div class="text-5xl text-purple-600 mb-4 text-center">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3 class="text-2xl font-semibold text-gray-800 mb-3 text-center">Laporan Kegiatan & Anggaran</h3>
                <p class="text-gray-600 text-center">
                    Dokumentasikan kegiatan desa dan laporan anggaran dengan rapi, mendukung transparansi dan akuntabilitas.
                </p>
            </div>

            <!-- Fitur 7: Integrasi AI (Coming Soon) -->
            <div class="bg-white p-8 rounded-lg shadow-lg transform hover:scale-105 transition-transform duration-300 animate-fade-in-up delay-600">
                <div class="text-5xl text-purple-600 mb-4 text-center">
                    <i class="fas fa-robot"></i>
                </div>
                <h3 class="text-2xl font-semibold text-gray-800 mb-3 text-center">Integrasi AI</h3>
                <p class="text-gray-600 text-center">
                    Fitur cerdas untuk membantu pembuatan proposal, RAB, dan narasi profil desa secara otomatis (dalam pengembangan).
                </p>
            </div>

            <!-- Fitur 8: Multi-Level User Access -->
            <div class="bg-white p-8 rounded-lg shadow-lg transform hover:scale-105 transition-transform duration-300 animate-fade-in-up delay-700">
                <div class="text-5xl text-purple-600 mb-4 text-center">
                    <i class="fas fa-user-shield"></i>
                </div>
                <h3 class="text-2xl font-semibold text-gray-800 mb-3 text-center">Akses Pengguna Berjenjang</h3>
                <p class="text-gray-600 text-center">
                    Manajemen peran pengguna (Super Admin, Admin Desa, RW, RT, Kader Posyandu) dengan hak akses yang disesuaikan.
                </p>
            </div>

             <!-- Fitur 9: Sistem Langganan (SaaS) -->
             <div class="bg-white p-8 rounded-lg shadow-lg transform hover:scale-105 transition-transform duration-300 animate-fade-in-up delay-800">
                <div class="text-5xl text-purple-600 mb-4 text-center">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <h3 class="text-2xl font-semibold text-gray-800 mb-3 text-center">Sistem Langganan (SaaS)</h3>
                <p class="text-gray-600 text-center">
                    Model bisnis SaaS dengan pengelolaan langganan desa, masa percobaan, dan notifikasi kadaluarsa.
                </p>
            </div>

        </div>
    </div>
</section>

<style>
    /* Basic animations for a smoother feel */
    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .animate-fade-in-down { animation: fadeInDown 0.8s ease-out forwards; }
    .animate-fade-in-up { animation: fadeInUp 0.8s ease-out forwards; }
    .delay-100 { animation-delay: 0.1s; }
    .delay-200 { animation-delay: 0.2s; }
    .delay-300 { animation-delay: 0.3s; }
    .delay-400 { animation-delay: 0.4s; }
    .delay-500 { animation-delay: 0.5s; }
    .delay-600 { animation-delay: 0.6s; }
    .delay-700 { animation-delay: 0.7s; }
    .delay-800 { animation-delay: 0.8s; }
</style>
@endsection
