@extends('layouts.public')

@section('title', 'Tentang Kami - DATA CERDAS')

@section('content')
<section class="bg-gradient-to-r from-purple-600 to-indigo-700 text-white py-20">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-5xl font-extrabold mb-4 animate-fade-in-down">Tentang DATA CERDAS</h1>
        <p class="text-xl opacity-90 animate-fade-in-up">
            Membangun Desa yang Lebih Baik dengan Data yang Akurat dan Terorganisir.
        </p>
    </div>
</section>

<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div class="animate-fade-in-left">
                <h2 class="text-4xl font-bold text-gray-800 mb-6">Visi Kami</h2>
                <p class="text-lg text-gray-700 leading-relaxed">
                    Menjadi platform terdepan yang memberdayakan setiap desa di Indonesia dengan informasi yang akurat, transparan, dan mudah diakses, demi terwujudnya tata kelola desa yang cerdas dan partisipatif. Kami percaya bahwa dengan data yang tepat, keputusan yang lebih baik dapat dibuat untuk kesejahteraan masyarakat.
                </p>
            </div>
            <div class="flex justify-center animate-fade-in-right">
                <img src="https://placehold.co/600x400/8B5CF6/FFFFFF?text=Visi+Kami" alt="Visi Kami" class="rounded-lg shadow-xl transform hover:scale-105 transition-transform duration-300">
            </div>
        </div>
    </div>
</section>

<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div class="flex justify-center order-2 md:order-1 animate-fade-in-left">
                <img src="https://placehold.co/600x400/6366F1/FFFFFF?text=Misi+Kami" alt="Misi Kami" class="rounded-lg shadow-xl transform hover:scale-105 transition-transform duration-300">
            </div>
            <div class="order-1 md:order-2 animate-fade-in-right">
                <h2 class="text-4xl font-bold text-gray-800 mb-6">Misi Kami</h2>
                <ul class="list-disc list-inside text-lg text-gray-700 space-y-3">
                    <li>Menyediakan platform manajemen data desa yang intuitif dan komprehensif.</li>
                    <li>Meningkatkan akurasi dan integritas data kependudukan, fasilitas, dan bantuan sosial.</li>
                    <li>Memfasilitasi pengambilan keputusan berbasis data bagi perangkat desa dan masyarakat.</li>
                    <li>Mendorong partisipasi aktif masyarakat dalam pembangunan desa melalui akses informasi.</li>
                    <li>Mengembangkan fitur-fitur inovatif yang relevan dengan kebutuhan desa.</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-4xl font-bold text-gray-800 mb-10 animate-fade-in-down">Tim Kami</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Contoh Anggota Tim 1 -->
            <div class="bg-white p-8 rounded-lg shadow-lg transform hover:scale-105 transition-transform duration-300 animate-fade-in-up">
                <img src="https://placehold.co/150x150/9CA3AF/FFFFFF?text=Foto+Tim+1" alt="Anggota Tim 1" class="w-32 h-32 rounded-full mx-auto mb-4 object-cover shadow-md">
                <h3 class="text-2xl font-semibold text-gray-800 mb-2">John Doe</h3>
                <p class="text-purple-600 font-medium mb-3">CEO & Founder</p>
                <p class="text-gray-600">Berpengalaman dalam pengembangan sistem informasi pemerintahan desa selama lebih dari 10 tahun.</p>
            </div>
            <!-- Contoh Anggota Tim 2 -->
            <div class="bg-white p-8 rounded-lg shadow-lg transform hover:scale-105 transition-transform duration-300 animate-fade-in-up delay-100">
                <img src="https://placehold.co/150x150/9CA3AF/FFFFFF?text=Foto+Tim+2" alt="Anggota Tim 2" class="w-32 h-32 rounded-full mx-auto mb-4 object-cover shadow-md">
                <h3 class="text-2xl font-semibold text-gray-800 mb-2">Jane Smith</h3>
                <p class="text-purple-600 font-medium mb-3">Chief Technology Officer</p>
                <p class="text-gray-600">Ahli dalam arsitektur software dan implementasi teknologi AI untuk analisis data.</p>
            </div>
            <!-- Contoh Anggota Tim 3 -->
            <div class="bg-white p-8 rounded-lg shadow-lg transform hover:scale-105 transition-transform duration-300 animate-fade-in-up delay-200">
                <img src="https://placehold.co/150x150/9CA3AF/FFFFFF?text=Foto+Tim+3" alt="Anggota Tim 3" class="w-32 h-32 rounded-full mx-auto mb-4 object-cover shadow-md">
                <h3 class="text-2xl font-semibold text-gray-800 mb-2">Peter Jones</h3>
                <p class="text-purple-600 font-medium mb-3">Head of Community Engagement</p>
                <p class="text-gray-600">Berkomitmen untuk membangun jembatan antara teknologi dan kebutuhan riil masyarakat desa.</p>
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
    @keyframes fadeInLeft {
        from { opacity: 0; transform: translateX(-20px); }
        to { opacity: 1; transform: translateX(0); }
    }
    @keyframes fadeInRight {
        from { opacity: 0; transform: translateX(20px); }
        to { opacity: 1; transform: translateX(0); }
    }

    .animate-fade-in-down { animation: fadeInDown 0.8s ease-out forwards; }
    .animate-fade-in-up { animation: fadeInUp 0.8s ease-out forwards; }
    .animate-fade-in-left { animation: fadeInLeft 0.8s ease-out forwards; }
    .animate-fade-in-right { animation: fadeInRight 0.8s ease-out forwards; }
    .delay-100 { animation-delay: 0.1s; }
    .delay-200 { animation-delay: 0.2s; }
</style>
@endsection

