@extends('layouts.portal')
@section('title', 'Pilih Program Bantuan')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Langkah 1: Pilih Program Bantuan</h4>
        </div>
        <div class="card-body">
            <p>Silakan pilih program bantuan yang akan diajukan untuk warga.</p>
            <div class="list-group">
                @forelse($kategoriBantuans as $kategori)
                    <a href="{{ route('portal.bantuan.pilihWarga', ['subdomain' => app('tenant')->subdomain, 'kategoriBantuan' => $kategori->id]) }}" 
                    class="list-group-item list-group-item-action">
                        <strong>{{ $kategori->nama_kategori }}</strong><br>
                        <small class="text-muted">{{ $kategori->keterangan }}</small>
                    </a>
                @empty
                    <p class="text-muted">Saat ini tidak ada program bantuan yang aktif.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@stop