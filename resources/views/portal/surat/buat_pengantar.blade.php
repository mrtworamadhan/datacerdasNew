@extends('layouts.portal')
@section('title', 'Pilih Warga untuk Buat Surat Pengantar')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Pilih Warga</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('portal.surat.pengantar', ['subdomain' => app('tenant')->subdomain]) }}" method="POST">
                @csrf
                <p>Silakan cari dan pilih warga yang akan dibuatkan surat berdasarkan Nama atau NIK.</p>

                <div class="mb-3">
                    <label for="warga_id" class="form-label">Cari Warga</label>
                    {{-- Select2 akan mengubah select ini menjadi field pencarian canggih --}}
                    <select name="warga_id" id="warga_id" class="form-control" style="width: 100%;" required></select>
                </div>

                <div class="form-group">
                    <label for="keperluan">Keperluan</label>
                    <textarea name="keperluan" id="keperluan" class="form-control" rows="3"
                        placeholder="Contoh: Untuk mengurus SKCK di kepolisian"
                        required>{{ old('keperluan') }}</textarea>
                </div>  

                <div class="d-grid mt-3">
                    <button type="submit" id="generate-pengantar-btn" class="btn btn-warning mb-3"><i
                            class="fas fa-print"></i> Buat & Download Surat Pengantar RT/RW</button>
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
        // $('#generate-pengantar-btn').on('click', function () {
        //     var wargaId = $('#warga_id').val();
        //     var keperluan = $('#keperluan').val();

        //     if (!wargaId || !keperluan) {
        //         alert('Silakan pilih warga dan isi keperluan terlebih dahulu.');
        //         return;
        //     }

        //     // Buat form sementara untuk kirim data ke tab baru
        //     var form = $('<form>', {
        //         'method': 'POST',
        //         'action': '{{ route("pengajuan-surat.generatePengantar",['subdomain' => app('tenant')->subdomain]) }}',
        //         'target': '_blank'
        //     });
        //     form.append($('<input>', { 'type': 'hidden', 'name': '_token', 'value': '{{ csrf_token() }}' }));
        //     form.append($('<input>', { 'type': 'hidden', 'name': 'warga_id', 'value': wargaId }));
        //     form.append($('<input>', { 'type': 'hidden', 'name': 'keperluan', 'value': keperluan }));

        //     $('body').append(form);
        //     form.submit();
        //     form.remove();
        // });
    </script>
@endpush