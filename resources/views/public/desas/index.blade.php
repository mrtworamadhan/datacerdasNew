@extends('layouts.public')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-8">Daftar Desa Pengguna DATA CERDAS</h1>
        <p class="text-center text-gray-600 mb-6">Temukan desa-desa yang telah bergabung dengan platform DATA CERDAS untuk tata kelola pemerintahan digital yang lebih baik.</p>

        {{-- Form Pencarian --}}
        <form action="{{ route('public.desas.index') }}" method="GET" class="mb-6">
            <div class="input-group flex">
                <input type="text" name="search" class="form-input rounded-l-lg p-2 border border-gray-300 flex-grow" placeholder="Cari nama desa, kepala desa, kecamatan, kota, atau provinsi..." value="{{ request('search') }}">
                <button class="bg-primary hover:bg-primary-600 text-white font-semibold py-2 px-4 rounded-r-lg" type="submit">
                    Cari
                </button>
                @if(request('search'))
                    <a href="{{ route('public.desas.index') }}" class="bg-secondary hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg ml-2">Reset</a>
                @endif
            </div>
        </form>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($desas as $desa)
                <div class="bg-white rounded-lg shadow-md overflow-hidden card-shadow">
                    <div class="p-4">
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ $desa->nama_desa }}</h3>
                        <p class="text-primary text-sm mb-2"><i class="fas fa-user-tie mr-2"></i>Kepala Desa: {{ $desa->nama_kades ?? '-' }}</p>
                        <p class="text-gray-600 text-sm mb-1"><i class="fas fa-map-marker-alt mr-2"></i>{{ $desa->kecamatan ?? '-' }}, {{ $desa->kota ?? '-' }}</p>
                        <p class="text-gray-600 text-sm"><i class="fas fa-globe-asia mr-2"></i>{{ $desa->provinsi ?? '-' }}</p>
                        
                        <div class="mt-4 flex justify-between items-center">
                            <span class="text-gray-500 text-xs">Status: {{ ucfirst($desa->subscription_status) }}</span>
                            {{-- Anda bisa menambahkan link ke halaman detail desa jika ada --}}
                            {{-- <a href="{{ route('desas.public.show', $desa->slug) }}" class="text-primary hover:underline text-sm">Lihat Detail</a> --}}
                        </div>
                    </div>
                </div>
            @empty
                <p class="col-span-full text-center text-gray-600">Tidak ada desa yang terdaftar sebagai pengguna DATA CERDAS.</p>
            @endforelse
        </div>
        <div class="mt-8">
            {{ $desas->appends(request()->query())->links('pagination::tailwind') }}
        </div>
    </div>
@endsection
