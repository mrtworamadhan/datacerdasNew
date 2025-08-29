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
                    <div class="text-center mb-4" style="padding-bottom: 10px;">
                        @if($suratSetting->path_kop_surat)
                            <img src="{{ asset('storage/' . $suratSetting->path_kop_surat) }}" alt="Kop Surat" style="max-width: 100%; height: auto;">
                        @elseif(isset($desa))
                            <div style="display: flex; align-items: center; justify-content: center; text-align: center;">
                                <div style="flex: 0 0 75px;">
                                    @if(isset($suratSetting) && $suratSetting->path_logo_pemerintah)
                                        <img src="{{ asset('storage/' . $suratSetting->path_logo_pemerintah) }}" alt="Logo"
                                            style="width: 50px; height: auto;">
                                    @else
                                        <img src="https://placehold.co/80x80?text=Logo" alt="Logo">
                                    @endif
                                </div>
                                <div style="flex: 1;">
                                    <p style="margin: 0; font-size: 9pt; font-weight: normal;">PEMERINTAH KABUPATEN BOGOR</p>
                                    <p style="margin: 0; font-size: 9pt; font-weight: bold;">KECAMATAN CIOMAS</p>
                                    <p style="margin: 0; font-size: 12pt; font-weight: bold;">DESA {{ strtoupper($desa->nama_desa) }}</p>
                                    <p style="margin: 0; font-size: 6pt;">Alamat : {{ $desa->alamat_desa }} website: {{ strtolower($desa->nama_desa) }}.datacerdas.com </p>
                                </div>
                            </div>
                        @else
                            <p class="text-muted">[ KOP SURAT AKAN TAMPIL DI SINI ]</p>
                        @endif
                    </div>
                    <h4 class="text-center font-weight-bold text-uppercase" style="text-decoration: underline; margin-bottom: 0.25rem;">{{ $jenisSurat->judul_surat }}</h4>
                    <p class="text-center" style="margin-top: 0; margin-bottom: 2rem;">Nomor : {{ $jenisSurat->klasifikasi->kode ?? '...' }} / ... / ... / {{ date('Y') }}</p>
                    <div id="isi_surat">
                        {!! $jenisSurat->isi_template !!}
                    </div>
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
