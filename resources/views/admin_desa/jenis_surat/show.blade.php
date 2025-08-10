@extends('admin.master')
@section('title', 'Preview Template Surat')
@push('css')
    <style>
        #isi_surat table {
            width: 100%;
            border-collapse: collapse;
            border: none !important;
            font-size: 16px;
        }
        #isi_surat td p {
            margin: 0;
            padding: 0;
            line-height: 1.5;
        }

        #isi_surat table tr td:first-child {
            width: 25%;
        }
        #isi_surat table tr td:nth-child(2) {
            width: 3%;
        }
        #isi_surat td,
        #isi_surat th {
            word-break: break-word;
            white-space: normal;
            padding: 1.5px;
            line-height: 1;
            vertical-align: top;
            height: 25px;
            border: none !important;
        }

        
    </style>
@endpush
@section('content_header')<h1 class="m-0 text-dark">Preview Template: {{ $jenisSurat->nama_surat }}</h1>@stop
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
             <div class="card-header">
                <h3 class="card-title">Preview Hasil Akhir</h3>
                <div class="card-tools">
                    <a href="{{ route('jenis-surat.edit', $jenisSurat) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Edit Template Ini</a>
                    <a href="{{ route('jenis-surat.index') }}" class="btn btn-sm btn-secondary">Kembali ke Daftar</a>
                </div>
            </div>
            <div class="card-body">
                <div id="print-area" style="background-color: #fff; border: 1px solid #ddd; padding: 2rem; max-width: 816px; margin: auto; font-family: 'Times New Roman', serif; font-size: 12pt;">
                    {{-- Kop Surat --}}
                    <div class="text-center mb-4" style="padding-bottom: 10px;">
                        @if($suratSetting->path_kop_surat)
                            <img src="{{ asset('storage/' . $suratSetting->path_kop_surat) }}" alt="Kop Surat" style="max-width: 100%; height: auto;">
                        @else
                            <p class="text-muted">[ KOP SURAT ]</p>
                        @endif
                    </div>
                    {{-- Judul Surat --}}
                    <h4 class="text-center font-weight-bold text-uppercase" style="text-decoration: underline; margin-bottom: 0.25rem;">{{ $jenisSurat->judul_surat }}</h4>
                    {{-- Nomor Surat --}}
                    <p class="text-center" style="margin-top: 0; margin-bottom: 2rem;">Nomor : {{ $jenisSurat->klasifikasi->kode ?? '...' }} / ... / ... / {{ date('Y') }}</p>
                    {{-- Isi Surat --}}
                    <div id="isi_surat">
                        {!! $jenisSurat->isi_template !!}
                    </div>
                    {{-- Tanda Tangan --}}
                    <div class="mt-5" style="width: 40%; float: right; text-align: center;">
                         <p>Desa Contoh, {{ \Carbon\Carbon::now()->translatedFormat("d F Y") }}</p>
                         <p>{{ $suratSetting->penanda_tangan_jabatan ?? 'Kepala Desa' }}</p>
                         <div class="text-center mb-2 " style="padding-bottom: 5px;">
                            @if(isset($suratSetting) && $suratSetting->path_ttd)
                                <img src="{{ asset('storage/' . $suratSetting->path_ttd) }}" alt="Kop Surat" style="max-width: 40%; height: auto;">
                            @else
                                <p class="text-muted">[ TTD AKAN TAMPIL DI SINI ]</p>
                            @endif
                        </div>
                         <p><strong><u>{{ $suratSetting->penanda_tangan_nama ?? 'Nama Kepala Desa' }}</u></strong></p>
                    </div>
                </div>
                <div>
                    @if($jenisSurat->persyaratan)
                <h5 class="mt-4">Daftar Persyaratan:</h5>
                <ul>
                    @foreach($jenisSurat->persyaratan as $syarat)
                        <li>{{ $syarat }}</li>
                    @endforeach
                </ul>
                @endif
                
                @if($jenisSurat->custom_fields)
                <h5 class="mt-4">Field Tambahan Kustom:</h5>
                <ul>
                    @foreach($jenisSurat->custom_fields as $field)
                        <li>{{ $field }}</li>
                    @endforeach
                </ul>
                @endif
                </div>
            </div>
        </div>
    </div>
</div>
@stop
