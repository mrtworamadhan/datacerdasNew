@extends('admin.master')

@section('content')
    <div class="container">
        <div class="row">
            {{-- KOLOM KIRI: FORM TAMBAH KADER --}}
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header">
                        <h4>Tambah Kader untuk {{ $posyandu->nama_posyandu }}</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('posyandu.kaders.store', $posyandu->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label>Cari Warga (berdasarkan NIK atau Nama)</label>
                                {{-- GANTI INPUT MENJADI SELECT --}}
                                <select id="select-warga" name="warga_id" class="form-control" required>
                                    {{-- Opsi akan diisi oleh Select2 --}}
                                </select>
                            </div>
                            <div class="mb-3">
                                <button type="submit" id="simpanBtn" class="btn btn-primary">Jadikan Kader</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: DAFTAR KADER AKTIF --}}
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header">
                        <h4>Daftar Kader Aktif</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>NIK</th>
                                    <th>Nama Kader</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($posyandu->kaders as $kader)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $kader->nik }}</td>
                                        <td>{{ $kader->nama_lengkap }}</td>
                                        <td>
                                            <form action="{{ route('posyandu.kaders.destroy', $kader->pivot->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Hapus kader ini?')">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Belum ada kader yang ditambahkan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    {{-- Select2 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    {{-- Select2 Bootstrap 5 Theme CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@endpush

{{-- Tempat untuk script AJAX kita nanti --}}
@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        $('#select-warga').select2({
            theme: 'bootstrap-5',
            placeholder: 'Ketik NIK atau Nama Warga...',
            allowClear: true,
            ajax: {
                // Route yang mengarah ke WargaController@searchWarga
                url: "{{ route('search.warga') }}",
                dataType: 'json',
                delay: 250, // Waktu tunggu setelah user berhenti mengetik

                data: function (params) {
                    return {
                        q: params.term // 'q' adalah parameter yang diharapkan oleh controllermu
                    };
                },
                processResults: function (data) {
                    // data.results adalah key yang dikirim oleh controllermu
                    return {
                        results: data.results
                    };
                },
                cache: true
            },
            minimumInputLength: 3 // Minimal 3 karakter untuk mulai mencari
        });
    });
</script>
@stop