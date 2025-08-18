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
            <h4 class="card-title">Update Status Warga</h4>
        </div>
        <div class="card-body">
            <p>Cari warga yang akan diperbarui statusnya berdasarkan Nama atau NIK.</p>
            <div class="mb-3">
                <select id="warga_search" class="form-control" style="width: 100%;"></select>
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
            $('#warga_search').select2({
                theme: 'bootstrap-5',
                placeholder: "Ketik Nama atau NIK Warga...",
                minimumInputLength: 3,
                ajax: {
                    url: "{{ route('search.warga', ['subdomain' => app('tenant')->subdomain]) }}",
                    dataType: 'json',
                    delay: 250,
                    processResults: function (data) { return { results: data.results }; },
                    cache: true
                }
            }).on('select2:select', function (e) {
                var wargaId = e.params.data.id;
                var url = "{{ route('portal.warga.editStatus', ['subdomain' => app('tenant')->subdomain, 'warga' => ':id']) }}".replace(':id', wargaId);

                // Ambil form edit via AJAX dan tampilkan
                $('#edit-form-container').html('<p class="text-center">Memuat...</p>').load(url);
            });
        });
    </script>
@endpush