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
                <div id="dynamic-fields-container" class="mt-4">
                    {{-- Checklist Persyaratan --}}
                    @if($jenisSurat->persyaratan && count($jenisSurat->persyaratan) > 0)
                        <div class="form-group">
                            <label>Mohon Siapkan dan Bawa Dokumen Berikut:</label>
                            @foreach($jenisSurat->persyaratan as $syarat)
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="syarat_{{ $loop->index }}" required>
                            <label class="custom-control-label" for="syarat_{{ $loop->index }}">{{ $syarat }}</label>
                            </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Field Kustom --}}
                    @if($jenisSurat->custom_fields && count($jenisSurat->custom_fields) > 0)
                        <div class="form-group mt-3">
                            <label>Isian Tambahan yang Diperlukan:</label>
                            @foreach($jenisSurat->custom_fields as $field)
                                @if($field !== 'tabel ahli waris')
                                    @php
                                        $fieldName = 'custom_fields[' . Str::slug($field, '_') . ']';
                                    @endphp
                                    <div class="form-group">
                                        <label for="{{ $fieldName }}">{{ $field }}</label>
                                        <input type="text" name="{{ $fieldName }}" class="form-control form-control-lg" placeholder="Masukkan {{ $field }}..." required>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
                @if(Str::contains(strtolower($jenisSurat->nama_surat), 'ahli waris'))
                <div id="ahli-waris-section" class="card card-outline card-info mt-4">
                    <div class="card-header"><h3 class="card-title">Data Ahli Waris</h3></div>
                    <div class="card-body">
                        <div id="ahli-waris-wrapper">
                            <div class="row ahli-waris-item mb-2 align-items-center">
                                <div class="col-md-4"><input type="text" name="ahli_waris[0][nama]" class="form-control form-control-lg" placeholder="Nama Lengkap"></div>
                                <div class="col-md-4"><input type="text" name="ahli_waris[0][nik]" class="form-control form-control-lg" placeholder="NIK"></div>
                                <div class="col-md-4"><input type="text" name="ahli_waris[0][hubungan]" class="form-control form-control-lg" placeholder="Hubungan Keluarga"></div>
                            </div>
                        </div>
                        <button type="button" id="tambah-ahli-waris-btn" class="btn btn-secondary mt-2"><i class="fas fa-plus"></i> Tambah Ahli Waris</button>
                    </div>
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