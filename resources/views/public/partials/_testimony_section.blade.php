<section class="bg-white py-12">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 gap-8 items-center lg:grid-cols-2">
      <!-- Video Thumbnail -->
      <div class="order-1 lg:order-none">
        <a href="#" @click.prevent="open = true; videoId = 'btlLfVkoj3g'" 
           class="relative block w-full aspect-video overflow-hidden rounded-xl shadow-md">
          <img src="{{ asset('images/welcome/testimoni-thumbnail.jpg') }}" alt="Video Testimoni"
               class="w-full h-full object-cover">
          <img src="https://portal-gcs-cdn.majoo.id/v2/icon/play-btn.svg" alt="Tombol Play"
               class="absolute inset-0 m-auto w-16 h-16">
        </a>
      </div>

      <!-- Caption -->
      <div class="space-y-4 order-2 lg:order-none text-center lg:text-left">
        <span class="text-sm font-semibold text-blue-600 uppercase block">Saatnya Majukan Desa Bersama Kami</span>
        <h3 class="text-2xl md:text-3xl font-bold text-gray-800">
          Lebih dari 100+ Desa Sudah Bertransformasi Bersama DATA CERDAS
        </h3>
        <a href="#" class="inline-flex items-center text-blue-600 hover:underline font-medium">
          Apa Kata Mereka Tentang Kami?
          <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" stroke-width="2"
               viewBox="0 0 24 24">
            <path d="M5 12h14M12 5l7 7-7 7"/>
          </svg>
        </a>
      </div>
    </div>
  </div>

  <!-- Modal Video (dengan Alpine.js) -->
  <div x-data="{ open: false, videoId: null }" x-show="open" x-cloak
       class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-80">
    <div class="relative w-full max-w-3xl p-4">
      <button @click="open = false; videoId = null"
              class="absolute top-2 right-2 text-white text-3xl font-bold">&times;</button>
      <div class="aspect-video bg-black rounded-lg overflow-hidden">
        <iframe :src="'https://www.youtube.com/embed/' + videoId + '?autoplay=1'" frameborder="0"
                allow="autoplay; encrypted-media" allowfullscreen
                class="w-full h-full"></iframe>
      </div>
    </div>
  </div>
</section>
