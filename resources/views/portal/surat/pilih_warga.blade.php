@extends('layouts.portal')
@section('title', 'Pilih Warga untuk Pengajuan Surat')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Langkah 1: Pilih Warga</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('portal.surat.pilihJenis', ['subdomain' => app('tenant')->subdomain]) }}" method="GET">
                <p>Silakan cari dan pilih warga yang akan dibuatkan surat berdasarkan Nama atau NIK.</p>

                <div class="mb-3">
                    <label for="warga_id" class="form-label">Cari Warga</label>
                    {{-- Select2 akan mengubah select ini menjadi field pencarian canggih --}}
                    <select name="warga_id" id="warga_id" class="form-control" style="width: 100%;" required></select>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Lanjutkan ke Pilih Jenis Surat</button>
                </div>
                
            </form>
        </div>
    </div>
</div>
@stop

@push('css')
    {{-- CSS untuk Select2 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endpush

@push('js')
    {{-- JS untuk Select2 --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#warga_id').select2({
                theme: 'bootstrap-5',
                placeholder: "Ketik Nama atau NIK Warga...",
                minimumInputLength: 3,
                ajax: {
                    url: "{{ route('search.warga', ['subdomain' => app('tenant')->subdomain]) }}",
                    dataType: 'json',
                    delay: 250,
                    processResults: function (data) {
                        return { results: data.results };
                    },
                    cache: true
                }
            });
        });
    </script>
@endpush