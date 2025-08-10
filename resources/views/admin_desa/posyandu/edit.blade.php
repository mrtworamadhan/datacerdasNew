@extends('admin.master')

@section('content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Edit Data Posyandu
                            <a href="{{ url('posyandu') }}" class="btn btn-danger float-end">Batal</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ url('posyandu/' . $posyandu->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label>Nama Posyandu</label>
                                <input type="text" name="nama_posyandu"
                                    class="form-control @error('nama_posyandu') is-invalid @enderror"
                                    value="{{ $posyandu->nama_posyandu }}">
                                @error('nama_posyandu') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label>Pilih RW</label>
                                <select name="rw_id" class="form-control @error('rw_id') is-invalid @enderror">
                                    <option value="">-- Pilih RW --</option>
                                    @foreach ($rws as $rw)
                                        <option value="{{ $rw->id }}" {{ $posyandu->rw_id == $rw->id ? 'selected' : '' }}>
                                            RW {{ $rw->nomor_rw }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('rw_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label>Alamat Lengkap Posyandu</label>
                                <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror"
                                    rows="3">{{ $posyandu->alamat }}</textarea>
                                @error('alamat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary">Perbarui</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection