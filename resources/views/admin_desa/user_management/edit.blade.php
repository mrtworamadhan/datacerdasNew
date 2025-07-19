    @extends('admin.master')

    @section('title', 'Edit Pengguna Desa - TataDesa')

    @section('content_header')
        <h1 class="m-0 text-dark">Edit Pengguna Desa</h1>
    @stop

    @section('content_main')
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Form Edit Pengguna</h3>
            </div>
            <form action="{{ route('admin_desa.user_management.update', $user) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Nama Pengguna</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="name" value="{{ old('name', $user->name) }}" required>
                        @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" id="email" value="{{ old('email', $user->email) }}" required>
                        @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="password">Password (Biarkan kosong jika tidak ingin diubah)</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="password">
                        @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control" id="password_confirmation">
                    </div>
                    <div class="form-group">
                        <label for="user_type">Tipe Pengguna</label>
                        <select name="user_type" class="form-control @error('user_type') is-invalid @enderror" id="user_type" required>
                            <option value="">Pilih Tipe Pengguna</option>
                            @foreach($userTypes as $type)
                                <option value="{{ $type }}" {{ old('user_type', $user->user_type) == $type ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                            @endforeach
                        </select>
                        @error('user_type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group" id="rw_id_group" style="{{ $user->user_type == 'admin_rw' || $user->user_type == 'admin_rt' ? '' : 'display:none;' }}">
                        <label for="rw_id">RW</label>
                        <select name="rw_id" class="form-control @error('rw_id') is-invalid @enderror" id="rw_id">
                            <option value="">Pilih RW</option>
                            @foreach($rws as $rw)
                                <option value="{{ $rw->id }}" {{ old('rw_id', $user->rw_id) == $rw->id ? 'selected' : '' }}>{{ $rw->nomor_rw }}</option>
                            @endforeach
                        </select>
                        @error('rw_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group" id="rt_id_group" style="{{ $user->user_type == 'admin_rt' ? '' : 'display:none;' }}">
                        <label for="rt_id">RT</label>
                        <select name="rt_id" class="form-control @error('rt_id') is-invalid @enderror" id="rt_id">
                            <option value="">Pilih RT</option>
                            @foreach($rts as $rt)
                                <option value="{{ $rt->id }}" {{ old('rt_id', $user->rt_id) == $rt->id ? 'selected' : '' }}>{{ $rt->nomor_rt }} (RW {{ $rt->rw->nomor_rw ?? '-' }})</option>
                            @endforeach
                        </select>
                        @error('rt_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Update Pengguna</button>
                    <a href="{{ route('admin_desa.user_management.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    @endsection

    @section('js')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const userTypeSelect = document.getElementById('user_type');
                const rwIdGroup = document.getElementById('rw_id_group');
                const rtIdGroup = document.getElementById('rt_id_group');

                function toggleRwRtFields() {
                    const selectedType = userTypeSelect.value;
                    if (selectedType === 'admin_rw' || selectedType === 'admin_rt') {
                        rwIdGroup.style.display = '';
                    } else {
                        rwIdGroup.style.display = 'none';
                        document.getElementById('rw_id').value = ''; // Clear selection
                    }

                    if (selectedType === 'admin_rt') {
                        rtIdGroup.style.display = '';
                    } else {
                        rtIdGroup.style.display = 'none';
                        document.getElementById('rt_id').value = ''; // Clear selection
                    }
                }

                userTypeSelect.addEventListener('change', toggleRwRtFields);

                // Initial call to set visibility based on current user type
                toggleRwRtFields();
            });
        </script>
    @endsection
    