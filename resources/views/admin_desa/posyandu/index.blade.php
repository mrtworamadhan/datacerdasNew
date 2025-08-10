@extends('admin.master')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Data Master Posyandu
                            <a href="{{ url('posyandu/create') }}" class="btn btn-primary float-end">Tambah Posyandu</a>
                        </h4>
                    </div>
                    <div class="card-body">

                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Posyandu</th>
                                    <th>RW</th>
                                    <th>Alamat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($posyandu as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->nama_posyandu }}</td>
                                        <td>{{ $item->rws->nomor_rw }}</td> {{-- Asumsi di tabel rws ada kolom 'nomor' --}}
                                        <td>{{ $item->alamat }}</td>
                                        <td>
                                            <a href="{{ url('posyandu/' . $item->id . '/edit') }}"
                                                class="btn btn-sm btn-success">Edit</a>
                                            <a href="{{ route('posyandu.kaders', $item->id) }}"
                                                class="btn btn-sm btn-info">Kelola Kader</a>
                                            <button type="button" class="btn btn-sm btn-primary show-detail-btn"
                                                data-posyandu-id="{{ $item->id }}">
                                                Detail
                                            </button>
                                            <a href="{{ route('laporan.posyandu.pdf', [
                                                'posyandu' => $item->id,
                                                'bulan' => now()->month, // Bulan sekarang
                                                'tahun' => now()->year // Tahun sekarang
                                            ]) }}" class="btn btn-sm btn-danger" target="_blank">
                                                Cetak Laporan
                                            </a>
                                            <form action="{{ url('posyandu/' . $item->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Data Posyandu belum tersedia.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="detailPosyanduModal" tabindex="-1" aria-labelledby="detailPosyanduModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailPosyanduModalLabel">Detail Posyandu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Nama Posyandu:</strong> <span id="detail_nama_posyandu"></span></p>
                <p><strong>Alamat:</strong> <span id="detail_alamat"></span></p>
                <p><strong>RW:</strong> <span id="detail_rw"></span></p>
                <hr>
                <h6>Daftar Kader:</h6>
                <ul id="detail_daftar_kader" class="list-group">
                {{-- Daftar kader akan diisi oleh JavaScript --}}
                </ul>
            </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    $('.show-detail-btn').click(function () {
        let posyanduId = $(this).data('posyandu-id');
        let url = "{{ url('posyandu-detail') }}/" + posyanduId;

        $.ajax({
            url: url,
            type: 'GET',
            success: function(data) {
                $('#detail_nama_posyandu').text(data.nama_posyandu);
                $('#detail_alamat').text(data.alamat);
                $('#detail_rw').text(data.rws.nomor_rw); // Asumsi kolom nomor di tabel rws

                let kaderList = $('#detail_daftar_kader');
                kaderList.empty(); // Kosongkan daftar sebelumnya

                if (data.kaders && data.kaders.length > 0) {
                    $.each(data.kaders, function(index, kader) {
                        kaderList.append('<li class="list-group-item">' + kader.nama_lengkap + ' (NIK: ' + kader.nik + ')</li>');
                    });
                } else {
                    kaderList.append('<li class="list-group-item">Belum ada kader.</li>');
                }

                // Tampilkan modal
                var myModal = new bootstrap.Modal(document.getElementById('detailPosyanduModal'));
                myModal.show();
            }
        });
    });
});
</script>
@stop