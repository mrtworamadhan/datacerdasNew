@extends('admin.master')

@section('title', 'Ubah Hak Akses')

@section('content_header')
    <h1 class="m-0 text-dark">Ubah Hak Akses untuk: {{ $user->name }}</h1>
@stop

@section('content')
    <form action="{{ route('permissions.update', ['userId' => $user->id]) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Centang hak akses yang ingin diberikan</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    {{-- Loop melalui semua permission yang ada, bagi menjadi 3 kolom --}}
                    @foreach ($permissions->chunk(ceil($permissions->count() / 3)) as $chunk)
                        <div class="col-md-4">
                            @foreach ($chunk as $permission)
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox"
                                               id="permission-{{ $permission->id }}"
                                               name="permissions[]"
                                               value="{{ $permission->name }}"
                                               {{-- INI KUNCINYA: Cek apakah nama permission ini ada di dalam array $userPermissions --}}
                                               {{ in_array($permission->name, $userPermissions) ? 'checked' : '' }}>
                                        <label for="permission-{{ $permission->id }}" class="custom-control-label">
                                            {{-- Mengubah 'kelola_warga' menjadi 'Kelola Warga' agar mudah dibaca --}}
                                            {{ ucfirst(str_replace('_', ' ', $permission->name)) }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('permissions.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </div>
    </form>
@stop