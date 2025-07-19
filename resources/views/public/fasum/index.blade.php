@extends('layouts.public')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-center text-gray-800 mb-8">Daftar Fasilitas Umum</h1>

    @if ($currentDesa)
    <h2 class="text-xl font-semibold text-center text-gray-700 mb-4">Desa: {{ $currentDesa->nama_desa }}</h2>
    @else
    <h2 class="text-xl font-semibold text-center text-gray-700 mb-4">Semua Fasilitas Umum Desa</h2>
    @endif

    {{-- Form Pencarian dan Filter --}}
    <form action="{{ route('public.fasum.index') }}" method="GET" class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="col-span-1">
                <input type="text" name="search" class="form-input rounded-lg p-2 border border-gray-300 w-full" placeholder="Cari nama, jenis, atau alamat Fasum..." value="{{ request('search') }}">
            </div>
            <div class="col-span-1">
                <select name="jenis_fasum" class="form-input rounded-lg p-2 border border-gray-300 w-full">
                    <option value="">-- Filter Kategori Fasum --</option>
                    @php
                    $jenisFasumOptions = ['Fasilitas Pendidikan',
                    'Fasilitas Kesehatan',
                    'Fasilitas Ibadah',
                    'Fasilitas Olahraga',
                    'Fasilitas Sanitasi & Lingkungan',
                    'Fasilitas Transportasi & Ekonomi',
                    'Fasilitas Umum Lainnya',];
                    @endphp
                    @foreach($jenisFasumOptions as $option)
                    <option value="{{ $option }}" {{ request('jenis_fasum') == $option ? 'selected' : '' }}>{{ $option }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-1">
                {{-- Filter Desa/Kecamatan (jika ada data desa/kecamatan yang bisa dipilih) --}}
                {{-- Ini akan memerlukan data $desas atau $kecamatans dari controller FasumController@indexPublic --}}
                <select name="desa_id" class="form-input rounded-lg p-2 border border-gray-300 w-full">
                    <option value="">-- Filter Berdasarkan Desa --</option>
                    {{-- Contoh: @foreach($allDesas as $desaOption) --}}
                    {{-- <option value="{{ $desaOption->id }}" {{ request('desa_id') == $desaOption->id ? 'selected' : '' }}>{{ $desaOption->nama_desa }}</option> --}}
                    {{-- @endforeach --}}
                    {{-- Untuk saat ini, kita bisa hardcode desa yang ada di seeder atau ambil dari database --}}
                    @php
                    // Ini hanya placeholder, Anda perlu mengambil data desa dari controller
                    $sampleDesas = \App\Models\Desa::all(); // Ambil semua desa yang ada
                    @endphp
                    @foreach($sampleDesas as $desaOption)
                    <option value="{{ $desaOption->id }}" {{ request('desa_id') == $desaOption->id ? 'selected' : '' }}>{{ $desaOption->nama_desa }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="mt-4 flex justify-end space-x-2">
            <button type="submit" class="bg-primary hover:bg-primary-600 text-white font-semibold py-2 px-4 rounded-lg">
                Filter
            </button>
            @if(request('search') || request('jenis_fasum') || request('desa_id'))
            <a href="{{ route('public.fasum.index', ['desa_id' => $currentDesa->id ?? '']) }}" class="bg-secondary hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg">Reset Filter</a>
            @endif
        </div>
    </form>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($fasums as $fasum)
        <div class="bg-white rounded-lg shadow-md overflow-hidden card-shadow">
            @if($fasum->photos->isNotEmpty())
            {{-- Perbaikan: Pastikan path gambar benar untuk public access --}}
            <img src="{{ Storage::url(str_replace('public/', '', $fasum->photos->first()->file_path)) }}" alt="{{ $fasum->nama_fasum }}" class="w-full h-48 object-cover">
            @else
            <img src="https://placehold.co/400x200/E0F2F7/2C3E50?text=No+Image" alt="No Image" class="w-full h-48 object-cover">
            @endif
            <div class="p-4">
                <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ $fasum->nama_fasum }}</h3>
                <p class="text-primary text-sm mb-2">{{ $fasum->jenis_fasum ?? 'Tidak Diketahui' }}</p>
                <p class="text-gray-600 text-sm mb-2">{{ Str::limit($fasum->deskripsi, 100) ?? 'Tidak ada deskripsi.' }}</p>
                <p class="text-gray-700 text-sm mb-1"><i class="fas fa-map-marker-alt mr-2"></i>{{ $fasum->alamat_lengkap ?? '-' }}</p>
                <p class="text-gray-700 text-sm">
                    <i class="fas fa-ruler-combined mr-2"></i>Luas: {{ $fasum->luas_area ?? '-' }}
                    <i class="fas fa-users mr-2 ml-4"></i>Kapasitas: {{ $fasum->kapasitas ?? '-' }}
                </p>
                <div class="mt-3 flex justify-between items-center">
                    <span class="text-gray-500 text-xs">RW {{ $fasum->rw->nomor_rw ?? '-' }}/RT {{ $fasum->rt->nomor_rt ?? '-' }}</span>
                    <span class="badge badge-{{ $fasum->kondisi == 'Baik' ? 'success' : ($fasum->kondisi == 'Sedang' ? 'warning' : 'danger') }}">{{ $fasum->kondisi ?? '-' }}</span>
                </div>
            </div>
        </div>
        @empty
        <p class="col-span-full text-center text-gray-600">Tidak ada Fasilitas Umum yang ditemukan.</p>
        @endforelse
    </div>
    <div class="mt-8">
        {{ $fasums->appends(request()->query())->links('pagination::tailwind') }} {{-- Menggunakan paginasi Tailwind --}}
    </div>
</div>
@endsection