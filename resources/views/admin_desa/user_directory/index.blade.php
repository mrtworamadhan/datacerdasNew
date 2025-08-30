    @extends('admin.master')

    @section('title', 'Profil Pengguna - TataDesa')

    @section('content_header')
        <h1 class="m-0 text-dark">Profil Pengguna</h1>
    @stop

    @section('content_main')
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Profil Pengguna Desa</h3>
            </div>
            <div class="card-body">
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

                {{-- Filter Section --}}
                <form action="{{ route('admin_desa.user_directory.index') }}" method="GET" class="mb-4">
                    <div class="form-row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="filter_rw_id">Filter RW:</label>
                                <select name="filter_rw_id" id="filter_rw_id" class="form-control">
                                    <option value="">Semua RW</option>
                                    @foreach($rws as $rw)
                                        <option value="{{ $rw->id }}" {{ request('filter_rw_id') == $rw->id ? 'selected' : '' }}>RW {{ $rw->nomor_rw }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="filter_rt_id">Filter RT:</label>
                                <select name="filter_rt_id" id="filter_rt_id" class="form-control">
                                    <option value="">Semua RT</option>
                                    @foreach($rts as $rt)
                                        <option value="{{ $rt->id }}" {{ request('filter_rt_id') == $rt->id ? 'selected' : '' }}>RT {{ $rt->nomor_rt }} (RW {{ $rt->rw->nomor_rw ?? '-' }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-info mr-2">Filter</button>
                            <a href="{{ route('admin_desa.user_directory.index') }}" class="btn btn-secondary">Reset Filter</a>
                        </div>
                    </div>
                </form>

                <form action="{{ route('admin_desa.user_directory.update_batch') }}" method="POST">
                    @csrf
                    <table class="table table-striped table-valign-middle">
                        <thead>
                            <tr>
                                <th>Nama Pengguna</th>
                                <th>Email</th>
                                <th>Tipe</th>
                                <th>RW</th>
                                <th>RT</th>
                                <th>Nama Ketua (Edit)</th>
                                <th>Status Akun</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $user->user_type)) }}</td>
                                    <td>{{ $user->rw->nomor_rw ?? '-' }}</td>
                                    <td>{{ $user->rt->nomor_rt ?? '-' }}</td>
                                    <td>
                                        @if ($user->user_type === 'admin_rw' && $user->rw)
                                            <input type="text" name="nama_ketua_rw[{{ $user->rw->id }}]" class="form-control form-control-sm" value="{{ old('nama_ketua_rw.'.$user->rw->id, $user->rw->nama_ketua) }}">
                                        @elseif ($user->user_type === 'admin_rt' && $user->rt)
                                            <input type="text" name="nama_ketua_rt[{{ $user->rt->id }}]" class="form-control form-control-sm" value="{{ old('nama_ketua_rt.'.$user->rt->id, $user->rt->nama_ketua) }}">
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $user->status_akun == 'aktif' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($user->status_akun ?? 'Aktif') }} {{-- Asumsi ada kolom status_akun --}}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada pengguna yang sesuai dengan filter.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Update Nama Ketua Terpilih</button>
                    </div>
                </form>
            </div>
            {{-- Tambahkan tautan paginasi di sini --}}
            <div class="card-footer clearfix">
                {{ $users->appends(request()->query())->links('pagination::bootstrap-4') }}
            </div>
        </div>
    @endsection
