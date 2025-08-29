@extends('admin.master')

@section('title', 'Manajemen Fasilitas Umum - Data Cerdas')

@section('content_header')
    <h1 class="m-0 text-dark">Manajemen Fasilitas Umum</h1>
@stop

@section('content_main')
    {{-- Dashboard Mini Fasum --}}
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $totalFasum }}</h3>
                    <p>Total Fasum</p>
                </div>
                <div class="icon">
                    <i class="fas fa-building"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $fasumRusak }}</h3>
                    <p>Fasum Rusak</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-6 col-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Fasum per Kategori</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="nav nav-pills flex-column">
                        @forelse ($fasumPerKategori as $item)
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    {{ $item->jenis_fasum ?? 'Tidak Diketahui' }}
                                    <span class="badge bg-primary float-right">{{ $item->total }}</span>
                                </a>
                            </li>
                        @empty
                            <li class="nav-item"><a href="#" class="nav-link text-muted">Tidak ada data kategori.</a></li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Card Khusus Admin Desa: RW Tanpa Fasum Spesifik --}}
    @if (Auth::user()->hasAnyRole('admin_desa', 'admin_umum'))
        @if (!empty($rwWithoutSpecificFasum))
            <div class="card card-warning card-outline">
                <div class="card-header">
                    <h3 class="card-title">RW Tanpa Fasilitas Penting</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <ul class="nav nav-pills flex-column">
                        @foreach ($rwWithoutSpecificFasum as $category => $rwList)
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    {{ $category }}
                                    <span class="badge bg-warning float-right">{{ implode(', ', $rwList) }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Fasilitas Umum</h3>
            <div class="card-tools">
                    <a href="{{ route('fasum.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Tambah Fasum
                    </a>
            </div>
        </div>
        <div class="card-body p-0">
            @if (session('success'))
                <div class="alert alert-success m-3">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger m-3">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Form Pencarian dan Filter --}}
            <form action="{{ route('fasum.index') }}" method="GET" class="p-3">
                <div class="row">
                    <div class="col-md-4 form-group">
                        <input type="text" name="search" class="form-control" placeholder="Cari nama, jenis, atau alamat Fasum..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4 form-group">
                        <select name="jenis_fasum" class="form-control">
                            <option value="">-- Filter Jenis Fasum --</option>
                            @foreach($jenisFasumOptions as $option)
                                <option value="{{ $option }}" {{ request('jenis_fasum') == $option ? 'selected' : '' }}>{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 form-group">
                        <select name="kondisi" class="form-control">
                            <option value="">-- Filter Kondisi --</option>
                            @foreach($kondisiOptions as $option)
                                <option value="{{ $option }}" {{ request('kondisi') == $option ? 'selected' : '' }}>{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 form-group">
                        <select name="status_kepemilikan" class="form-control">
                            <option value="">-- Filter Kepemilikan --</option>
                            @foreach($statusKepemilikanOptions as $option)
                                <option value="{{ $option }}" {{ request('status_kepemilikan') == $option ? 'selected' : '' }}>{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- Filter RW/RT hanya untuk Admin Desa/Super Admin --}}
                    @if (Auth::user()->hasAnyRole('admin_desa', 'admin_umum'))
                        <div class="col-md-2 form-group">
                            <select name="rw_id" id="filter_rw_id" class="form-control">
                                <option value="">-- Filter RW --</option>
                                @foreach($rws as $rw)
                                    <option value="{{ $rw->id }}" {{ request('rw_id') == $rw->id ? 'selected' : '' }}>RW {{ $rw->nomor_rw }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 form-group">
                            <select name="rt_id" id="filter_rt_id" class="form-control" {{ request('rw_id') ? '' : 'disabled' }}>
                                <option value="">-- Filter RT --</option>
                                {{-- Options will be loaded by JS --}}
                            </select>
                        </div>
                    @endif
                    <div class="col-md-4 form-group d-flex align-items-end">
                        <button class="btn btn-info mr-2" type="submit">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        @if(request('search') || request('jenis_fasum') || request('kondisi') || request('status_kepemilikan') || request('rw_id') || request('rt_id'))
                            <a href="{{ route('fasum.index') }}" class="btn btn-secondary">Reset</a>
                        @endif
                    </div>
                </div>
            </form>

            <table class="table table-striped table-valign-middle table-responsive">
                <thead>
                    <tr>
                        <th style="width: 5%;">No.</th>
                        <th style="width: 15%;">Nama Fasum</th>
                        <th style="width: 10%;">Jenis</th>
                        <th style="width: 15%;">Alamat</th>
                        <th style="width: 8%;">RW/RT</th>
                        <th style="width: 8%;">Kondisi</th>
                        <th style="width: 8%;">Panjang</th>
                        <th style="width: 8%;">Lebar</th>
                        <th style="width: 10%;">Pengelola</th>
                        <th style="width: 10%;">Kepemilikan</th>
                        <th style="width: 15%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($fasums as $index => $fasum)
                        <tr>
                            <td>{{ $fasums->firstItem() + $index }}</td>
                            <td>
                                <strong>{{ $fasum->nama_fasum }}</strong><br>
                                <small>{{ Str::limit($fasum->deskripsi, 50) }}</small>
                            </td>
                            <td><span class="badge badge-info">{{ $fasum->kategori ?? '-' }}</span></td>
                            <td>{{ Str::limit($fasum->alamat_lengkap, 50) ?? '-' }}</td>
                            <td>RW {{ $fasum->rw->nomor_rw ?? '-' }}/RT {{ $fasum->rt->nomor_rt ?? '-' }}</td>
                            <td>
                                @php
                                    $badgeClass = 'secondary';
                                    if ($fasum->status_kondisi == 'Baik') $badgeClass = 'success';
                                    elseif ($fasum->status_kondisi == 'Sedang') $badgeClass = 'warning';
                                    elseif ($fasum->status_kondisi == 'Rusak') $badgeClass = 'danger';
                                @endphp
                                <span class="badge badge-{{ $badgeClass }}">{{ $fasum->status_kondisi ?? '-' }}</span>
                            </td>
                            <td>{{ $fasum->panjang ?? '-' }}</td>
                            <td>{{ $fasum->lebar ?? '-' }}</td>
                            <td>{{ $fasum->kontak_pengelola ?? '-' }}</td>
                            <td>{{ $fasum->status_kepemilikan ?? '-' }}</td>
                            <td>
                                <a href="{{ route('fasum.show', $fasum) }}" class="btn btn-info btn-xs"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('fasum.edit', $fasum) }}" class="btn btn-warning btn-xs"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('fasum.destroy', $fasum) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Yakin ingin menghapus Fasilitas Umum ini?')"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center">Tidak ada Fasilitas Umum yang ditemukan di wilayah Anda.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            {{ $fasums->appends(request()->query())->links('pagination::bootstrap-4') }}
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function () {
            // Logika dropdown RT dinamis berdasarkan RW untuk filter
            const filterRwSelect = $('#filter_rw_id');
            const filterRtSelect = $('#filter_rt_id');
            const initialFilterRwId = filterRwSelect.val();
            const initialFilterRtId = "{{ request('rt_id') }}"; // Ambil dari request, bukan old input

            function loadFilterRts(rwId, selectedRtId = null) {
                filterRtSelect.empty().append('<option value="">-- Filter RT --</option>');
                filterRtSelect.prop('disabled', true);

                if (rwId) {
                    $.ajax({
                        url: "{{ route('api.rts-by-rw') }}",
                        type: 'GET',
                        data: { rw_id: rwId },
                        success: function (data) {
                            $.each(data, function (key, value) {
                                filterRtSelect.append('<option value="' + value.id + '">' + 'RT ' + value.nomor_rt + '</option>');
                            });
                            if (selectedRtId) {
                                filterRtSelect.val(selectedRtId);
                            }
                            filterRtSelect.prop('disabled', false);
                        },
                        error: function (xhr, status, error) {
                            console.error("Error loading filter RTs:", error);
                            filterRtSelect.html('<option value="">Gagal memuat RT</option>').prop('disabled', true);
                        }
                    });
                } else {
                    filterRtSelect.html('<option value="">-- Filter RT --</option>').prop('disabled', true);
                }
            }

            // Panggil saat halaman pertama kali dimuat (jika ada filter RW)
            if (initialFilterRwId) {
                loadFilterRts(initialFilterRwId, initialFilterRtId);
            }

            // Panggil saat pilihan RW filter berubah
            filterRwSelect.on('change', function () {
                loadFilterRts($(this).val());
            });
        });
    </script>
@stop
