@extends('layouts.portal')
@section('title', 'Update Status Warga')

@section('content')
<div class="container">
    @foreach (['success', 'error'] as $msg)
        @if (session($msg))
            <div class="alert alert-{{ $msg == 'error' ? 'danger' : $msg }} alert-dismissible fade show" role="alert">
                {{ session($msg) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    @endforeach
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Update Status Keluarga</h4>
        </div>
        <div class="card-body">
            <p>Cari KK yang akan diperbarui statusnya berdasarkan No KK.</p>
            <div class="mb-3">
                <select id="kk_search" class="form-control" style="width: 100%;"></select>
            </div>
            <div id="edit-form-container">
                {{-- Form edit akan dimuat di sini via AJAX --}}
            </div>
        </div>
    </div>
</div>
@stop

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#kk_search').select2({
                theme: 'bootstrap-5',
                placeholder: "Ketik No KK atau Nama Kepala...",
                minimumInputLength: 3,
                ajax: {
                    url: "{{ route('search.kkFast', ['subdomain' => app('tenant')->subdomain]) }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return { q: params.term }; // kirim keyword ke backend
                    },
                    processResults: function (data) {
                        return { results: data.results };
                    },
                    cache: true
                }
            }).on('select2:select', function (e) {
            var kkId = e.params.data.id; 
            
            // ======================================================
            // === PERBAIKAN UTAMA: Ganti 'KartuKeluarga' menjadi 'keluarga' ===
            // ======================================================
            var url = "{{ route('portal.kartuKeluarga.edit', ['subdomain' => app('tenant')->subdomain, 'kartuKeluarga' => 'KARTU_ID']) }}"
                .replace('KARTU_ID', kkId);
            // ======================================================

            $('#edit-form-container').html('<p class="text-center">Memuat...</p>')
                .load(url);
            });
        });
    </script>

@endpush