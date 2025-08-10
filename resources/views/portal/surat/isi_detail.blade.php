@extends('layouts.portal')
@section('title', 'Isi Detail Surat')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Langkah 3: Lengkapi Detail & Ajukan</h4>
        </div>
        <div class="card-body">
            <div class="alert alert-light border mb-4">
                <p class="mb-1"><strong>Jenis Surat:</strong> {{ $jenisSurat->nama_surat }}</p>
                <p class="mb-1"><strong>Untuk Warga:</strong> {{ $warga->nama_lengkap }}</p>
            </div>

            <form action="{{ route('portal.surat.store', ['subdomain' => app('tenant')->subdomain]) }}" method="POST">
                @csrf
                <input type="hidden" name="warga_id" value="{{ $warga->id }}">
                <input type="hidden" name="jenis_surat_id" value="{{ $jenisSurat->id }}">

                {{-- Menampilkan field dinamis --}}
                @if($jenisSurat->custom_fields && count($jenisSurat->custom_fields) > 0)
                    <div class="mb-3">
                        <label class="form-label"><strong>Isian Tambahan yang Diperlukan:</strong></label>
                        @foreach($jenisSurat->custom_fields as $field)
                            @php
                                $fieldName = 'custom_fields[' . Str::slug($field, '_') . ']';
                            @endphp
                            <div class="form-group">
                                <label for="{{ $fieldName }}">{{ $field }}</label>
                                <input type="text" name="{{ $fieldName }}" class="form-control" placeholder="Masukkan {{ $field }}..." required>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="mb-3">
                    <label for="keperluan" class="form-label">Keperluan</label>
                    <textarea name="keperluan" id="keperluan" class="form-control" rows="3" placeholder="Contoh: Untuk melamar pekerjaan" required></textarea>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">Ajukan Surat Sekarang</button>
                    <a href="{{ route('portal.surat.create', ['subdomain' => app('tenant')->subdomain]) }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@stop