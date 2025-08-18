@extends('layouts.portal')
@section('title', 'Cari Rekam Medis Anak')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endpush

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header">
            <h4 class="mb-0">Pencarian Rekam Medis Anak</h4>
        </div>
        <div class="card-body text-center" style="padding: 50px 20px;">
            <p class="text-muted">Ketik Nama atau NIK anak yang ingin Anda lihat riwayat kesehatannya.</p>
            
            {{-- Dropdown Pencarian AJAX --}}
            <div style="max-width: 600px; margin: auto;">
                <select id="cari_anak_select" class="form-select form-select-lg" 
                        data-find-url="{{ route('portal.posyandu.findAnak', ['subdomain' => $subdomain]) }}">
                </select>
            </div>

        </div>
    </div>
</div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            var anakSelect = $('#cari_anak_select');
            anakSelect.select2({
                theme: 'bootstrap-5',
                placeholder: '-- Ketik Nama atau NIK Anak --',
                ajax: {
                    url: anakSelect.data('find-url'),
                    dataType: 'json',
                    delay: 250,
                    processResults: function (data) {
                        return { results: data };
                    },
                    cache: true
                }
            });

            // Saat anak dipilih, redirect ke halaman detailnya
            anakSelect.on('change', function() {
                var url = $(this).val();
                if (url) {
                    window.location = url;
                }
            });
        });
    </script>
@endpush