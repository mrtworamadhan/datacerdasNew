@extends('layouts.public')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-8">Syarat & Ketentuan Layanan DATA CERDAS</h1>

        <div class="bg-white p-8 rounded-xl shadow-md card-shadow text-gray-700 leading-relaxed">
            <p class="mb-4">
                Selamat datang di DATA CERDAS, platform digital yang disediakan oleh [Nama Perusahaan Anda] ("Kami", "Platform"). Dengan mengakses atau menggunakan aplikasi kami, Anda ("Pengguna", "Anda") setuju untuk terikat oleh Syarat & Ketentuan Layanan ini ("Syarat"). Harap baca dengan seksama.
            </p>

            <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">1. Penerimaan Syarat</h3>
            <p class="mb-4">
                Dengan mendaftar, mengakses, atau menggunakan layanan DATA CERDAS, Anda menyatakan bahwa Anda telah membaca, memahami, dan menyetujui untuk terikat oleh Syarat ini, serta Kebijakan Privasi kami. Jika Anda tidak setuju dengan Syarat ini, Anda tidak diizinkan untuk menggunakan layanan kami.
            </p>

            <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">2. Perubahan Syarat</h3>
            <p class="mb-4">
                Kami berhak untuk mengubah atau memodifikasi Syarat ini kapan saja atas kebijakan kami sendiri. Setiap perubahan akan segera berlaku setelah diposting di halaman ini. Penggunaan Anda yang berkelanjutan atas layanan kami setelah perubahan tersebut merupakan persetujuan Anda terhadap Syarat yang direvisi.
            </p>

            <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">3. Akun Pengguna</h3>
            <ul class="list-disc list-inside ml-4 mb-4">
                <li>Anda harus berusia minimal 18 tahun untuk menggunakan layanan kami.</li>
                <li>Anda bertanggung jawab untuk menjaga kerahasiaan informasi akun Anda (username dan password).</li>
                <li>Anda bertanggung jawab atas semua aktivitas yang terjadi di bawah akun Anda.</li>
                <li>Anda setuju untuk segera memberitahu kami tentang penggunaan akun Anda yang tidak sah.</li>
            </ul>

            <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">4. Penggunaan Layanan</h3>
            <ul class="list-disc list-inside ml-4 mb-4">
                <li>Anda setuju untuk menggunakan DATA CERDAS hanya untuk tujuan yang sah dan sesuai dengan Syarat ini.</li>
                <li>Anda tidak boleh menggunakan layanan kami untuk tujuan ilegal atau tidak sah.</li>
                <li>Anda tidak boleh mencoba mengakses sistem atau data yang tidak diizinkan.</li>
                <li>Anda bertanggung jawab atas keakuratan dan legalitas data yang Anda masukkan ke dalam sistem.</li>
            </ul>

            <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">5. Langganan dan Pembayaran</h3>
            <ul class="list-disc list-inside ml-4 mb-4">
                <li>Layanan DATA CERDAS mungkin memerlukan langganan berbayar. Detail harga dan paket akan dijelaskan secara terpisah.</li>
                <li>Pembayaran harus dilakukan sesuai dengan ketentuan yang berlaku. Akses ke fitur-fitur tertentu dapat dibatasi jika langganan berakhir atau pembayaran tertunda.</li>
                <li>Semua pembayaran bersifat final dan tidak dapat dikembalikan, kecuali ditentukan lain oleh kebijakan pengembalian dana kami.</li>
            </ul>

            <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">6. Hak Kekayaan Intelektual</h3>
            <p class="mb-4">
                Semua hak kekayaan intelektual atas DATA CERDAS, termasuk perangkat lunak, desain, logo, dan konten, adalah milik [Nama Perusahaan Anda] atau pemberi lisensinya. Anda tidak diizinkan untuk menyalin, memodifikasi, mendistribusikan, atau merekayasa balik bagian mana pun dari platform tanpa izin tertulis dari kami.
            </p>

            <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">7. Penolakan Jaminan</h3>
            <p class="mb-4">
                Layanan DATA CERDAS disediakan "apa adanya" tanpa jaminan apa pun, baik tersurat maupun tersirat. Kami tidak menjamin bahwa layanan akan bebas dari kesalahan, tidak terganggu, atau aman.
            </p>

            <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">8. Batasan Tanggung Jawab</h3>
            <p class="mb-4">
                Sejauh diizinkan oleh hukum, [Nama Perusahaan Anda] tidak akan bertanggung jawab atas kerugian tidak langsung, insidental, khusus, konsekuensial, atau hukuman, atau kerugian keuntungan atau pendapatan, baik yang terjadi secara langsung maupun tidak langsung, atau kehilangan data, penggunaan, niat baik, atau kerugian tidak berwujud lainnya, yang diakibatkan oleh akses Anda ke atau penggunaan atau ketidakmampuan untuk mengakses atau menggunakan layanan.
            </p>

            <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">9. Pengakhiran</h3>
            <p class="mb-4">
                Kami dapat menangguhkan atau mengakhiri akses Anda ke layanan kami kapan saja, tanpa pemberitahuan sebelumnya atau tanggung jawab, jika Anda melanggar Syarat ini.
            </p>

            <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">10. Hukum yang Mengatur</h3>
            <p class="mb-4">
                Syarat ini akan diatur dan ditafsirkan sesuai dengan hukum Republik Indonesia, tanpa memperhatikan pertentangan ketentuan hukumnya.
            </p>

            <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">11. Kontak Kami</h3>
            <p class="mb-4">
                Jika Anda memiliki pertanyaan tentang Syarat ini, silakan hubungi kami di:
                <br>Email: {{ $companySettings['company_email'] ?? 'info@desacerdas.id' }}
                <br>WhatsApp: {{ $companySettings['whatsapp_number'] ?? '+6281234567890' }}
                <br>Alamat: {{ $companySettings['company_address'] ?? 'Alamat Perusahaan' }}
            </p>

            <p class="mt-8 text-sm text-gray-600">
                Terakhir diperbarui: {{ date('d F Y') }}
            </p>
        </div>
    </div>
@endsection
