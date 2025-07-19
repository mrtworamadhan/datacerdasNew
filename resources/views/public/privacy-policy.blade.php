@extends('layouts.public')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-8">Kebijakan Privasi DATA CERDAS</h1>

        <div class="bg-white p-8 rounded-xl shadow-md card-shadow text-gray-700 leading-relaxed">
            <p class="mb-4">
                Selamat datang di DATA CERDAS, platform digital yang dirancang untuk membantu tata kelola pemerintahan desa menjadi lebih efisien dan cerdas. Privasi pengguna adalah prioritas utama kami. Kebijakan Privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, mengungkapkan, dan melindungi informasi Anda ketika Anda menggunakan aplikasi kami.
            </p>

            <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">1. Informasi yang Kami Kumpulkan</h3>
            <p class="mb-2">Kami mengumpulkan berbagai jenis informasi untuk menyediakan dan meningkatkan layanan kami kepada Anda, termasuk:</p>
            <ul class="list-disc list-inside ml-4 mb-4">
                <li>Informasi Akun: Nama pengguna, alamat email, jenis akun (Super Admin, Admin Desa, Admin RW, Admin RT, Kader Posyandu).</li>
                <li>Informasi Desa: Nama desa, alamat lengkap, kecamatan, kota, provinsi, kode pos, nama kepala desa, status langganan.</li>
                <li>Data Kependudukan: NIK, nama lengkap, tempat/tanggal lahir, jenis kelamin, agama, status perkawinan, pekerjaan, pendidikan, kewarganegaraan, golongan darah, alamat lengkap warga, hubungan keluarga, status kependudukan.</li>
                <li>Data Fasilitas Umum: Nama fasum, jenis, deskripsi, alamat, koordinat (latitude, longitude), kondisi, tahun berdiri, luas area, kapasitas, kontak pengelola, status kepemilikan, dan foto-foto terkait.</li>
                <li>Data Lembaga & Kegiatan: Nama lembaga, deskripsi, SK Kepala Desa, pengurus, serta detail kegiatan.</li>
                <li>Data Bantuan Sosial: Kategori bantuan, kriteria, detail penerima, tanggal pengajuan, keterangan, status permohonan, dan foto/dokumen tambahan.</li>
                <li>Data Surat: Jenis surat, detail pengajuan, status, dan file yang digenerate.</li>
                <li>Data Kesehatan (jika diaktifkan): Informasi terkait ibu hamil, balita, dan anak (misal: berat badan, tinggi badan, imunisasi).</li>
                <li>Data Teknis: Informasi perangkat, alamat IP, jenis browser, waktu akses, dan halaman yang dikunjungi.</li>
            </ul>

            <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">2. Bagaimana Kami Menggunakan Informasi Anda</h3>
            <p class="mb-2">Informasi yang kami kumpulkan digunakan untuk berbagai tujuan, antara lain:</p>
            <ul class="list-disc list-inside ml-4 mb-4">
                <li>Menyediakan, mengoperasikan, dan memelihara aplikasi DATA CERDAS.</li>
                <li>Meningkatkan, mempersonalisasi, dan memperluas layanan kami.</li>
                <li>Memahami dan menganalisis bagaimana Anda menggunakan layanan kami.</li>
                <li>Mengembangkan produk, layanan, fitur, dan fungsionalitas baru.</li>
                <li>Berkomunikasi dengan Anda, baik secara langsung maupun melalui mitra kami, untuk layanan pelanggan, pembaruan, dan informasi terkait lainnya.</li>
                <li>Memproses transaksi Anda dan mengelola langganan desa.</li>
                <li>Mengirim email verifikasi, notifikasi, dan pembaruan penting.</li>
                <li>Mencegah penipuan dan menjaga keamanan aplikasi kami.</li>
                <li>Melakukan analisis dan penelitian untuk tujuan internal.</li>
            </ul>

            <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">3. Pengungkapan Informasi Anda</h3>
            <p class="mb-4">Kami tidak akan menjual atau menyewakan informasi pribadi Anda kepada pihak ketiga. Kami dapat mengungkapkan informasi Anda dalam situasi berikut:</p>
            <ul class="list-disc list-inside ml-4 mb-4">
                <li>Dengan Persetujuan Anda: Kami dapat mengungkapkan informasi Anda untuk tujuan apa pun dengan persetujuan Anda.</li>
                <li>Penyedia Layanan: Kami dapat berbagi informasi dengan penyedia layanan pihak ketiga yang melakukan layanan atas nama kami (misal: penyedia hosting, layanan email, layanan OCR). Mereka hanya memiliki akses ke informasi yang diperlukan untuk melakukan fungsi tersebut dan dilarang menggunakannya untuk tujuan lain.</li>
                <li>Kewajiban Hukum: Kami dapat mengungkapkan informasi Anda jika diwajibkan oleh hukum atau dalam menanggapi permintaan yang sah dari otoritas publik.</li>
                <li>Perlindungan Hak: Kami dapat mengungkapkan informasi untuk melindungi hak, properti, atau keamanan DATA CERDAS, pengguna kami, atau publik.</li>
            </ul>

            <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">4. Keamanan Data</h3>
            <p class="mb-4">Kami menerapkan langkah-langkah keamanan teknis dan organisasi yang wajar untuk melindungi informasi pribadi Anda dari akses, penggunaan, atau pengungkapan yang tidak sah. Namun, tidak ada metode transmisi melalui internet atau metode penyimpanan elektronik yang 100% aman.</p>

            <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">5. Hak-Hak Anda</h3>
            <p class="mb-4">Anda memiliki hak untuk mengakses, memperbarui, atau menghapus informasi pribadi Anda yang kami miliki. Untuk permintaan tersebut, silakan hubungi kami melalui informasi kontak yang tersedia.</p>

            <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">6. Perubahan pada Kebijakan Privasi Ini</h3>
            <p class="mb-4">Kami dapat memperbarui Kebijakan Privasi kami dari waktu ke waktu. Kami akan memberitahu Anda tentang setiap perubahan dengan memposting Kebijakan Privasi baru di halaman ini. Anda disarankan untuk meninjau Kebijakan Privasi ini secara berkala untuk setiap perubahan.</p>

            <p class="mt-8 text-sm text-gray-600">
                Terakhir diperbarui: {{ date('d F Y') }}
            </p>
        </div>
    </div>
@endsection
