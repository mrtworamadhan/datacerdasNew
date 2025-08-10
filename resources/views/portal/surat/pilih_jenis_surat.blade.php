@extends('layouts.portal')
@section('title', 'Pilih Jenis Surat')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Langkah 2: Pilih Jenis Surat</h4>
        </div>
        <div class="card-body">
            <div class="alert alert-light border">
                <p class="mb-1"><strong>Mengajukan untuk:</strong></p>
                <h5>{{ $warga->nama_lengkap }}</h5>
                <p class="mb-0">NIK: {{ $warga->nik }}</p>
            </div>

            <form action="{{ route('portal.surat.isiDetail', ['subdomain' => app('tenant')->subdomain]) }}" method="GET">
                @csrf
                <input type="hidden" name="warga_id" value="{{ $warga->id }}">

                <div class="mb-3">
                    <label for="jenis_surat_id" class="form-label">Jenis Surat yang Akan Dibuat</label>
                    <select name="jenis_surat_id" id="jenis_surat_id" class="form-select" required>
                        <option value="">-- Pilih Jenis Surat --</option>
                        @foreach($jenisSurats as $jenis)
                            <option value="{{ $jenis->id }}">{{ $jenis->nama_surat }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Di sini nanti akan muncul field dinamis (persyaratan & isian tambahan) --}}
                <div id="dynamic-fields-container" class="mt-4"></div>

                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">Lanjutkan ke Pengisian Detail</button>
                    <a href="{{ route('portal.surat.create', ['subdomain' => app('tenant')->subdomain]) }}" class="btn btn-secondary">Kembali (Ganti Warga)</a>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@push('js')
    {{-- Di sini nanti kita akan tambahkan script AJAX untuk menampilkan field dinamis --}}
@endpush