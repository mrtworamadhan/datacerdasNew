@extends('admin.master')

@section('title', 'Generate Akun RW/RT/Kader - Desa Cerdas')

@section('content_header')
    <h1 class="m-0 text-dark">Generate Akun RW/RT/Kader</h1>
@stop

@section('content_main')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Generate Akun untuk {{ $desa->nama_desa }}</h3>
        </div>
        <div class="card-body">
            @if (session('success_rw'))
                <div class="alert alert-success">
                    {{ session('success_rw') }}
                </div>
            @endif
            @if (session('success_rt'))
                <div class="alert alert-success">
                    {{ session('success_rt') }}
                </div>
            @endif
            @if (session('success_kader'))
                <div class="alert alert-success">
                    {{ session('success_kader') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <p class="text-danger"><strong>Password default untuk akun yang digenerate adalah: password123</strong>. Harap informasikan kepada pengguna terkait dan sarankan untuk segera mengubah password mereka.</p>
            <hr>

            <div class="row">
                {{-- Form Generate Akun RW --}}
                <div class="col-md-6">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h5 class="card-title">Generate Akun RW</h5>
                        </div>
                        <form action="{{ route('admin_desa.user_management.generate_rws') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="jumlah_rw">Jumlah RW yang Akan Digenerate</label>
                                    <input type="number" name="jumlah_rw" class="form-control @error('jumlah_rw') is-invalid @enderror" id="jumlah_rw" value="{{ old('jumlah_rw', $currentRwCount) }}" min="0" required>
                                    @error('jumlah_rw') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    <small class="form-text text-muted">Saat ini ada {{ $currentRwCount }} akun RW yang terdaftar.</small>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Generate / Update Akun RW</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Form Generate Akun Kader Posyandu --}}
                <div class="col-md-6">
                    <div class="card card-success card-outline">
                        <div class="card-header">
                            <h5 class="card-title">Generate Akun Kader Posyandu</h5>
                        </div>
                        {{-- Arahkan action ke route yang benar --}}
                        <form action="{{ route('admin_desa.user_management.generate_kaders') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    {{-- Ganti label dari RW menjadi Posyandu --}}
                                    <label for="posyandu_id">Pilih Posyandu untuk Akun Kader</label>
                                    
                                    {{-- Sesuaikan nama input dan error handling --}}
                                    <select name="posyandu_id" class="form-control @error('posyandu_id') is-invalid @enderror" id="posyandu_id" required>
                                        <option value="">-- Pilih Posyandu --</option>
                                        
                                        {{-- Loop sekarang menggunakan variabel $posyandus --}}
                                        @foreach($posyandus as $posyandu)
                                            @php
                                                // Cek apakah posyandu ini sudah punya kader
                                                $hasKader = $posyandusWithKader->contains($posyandu->id);
                                            @endphp
                                            <option value="{{ $posyandu->id }}" 
                                                {{ old('posyandu_id') == $posyandu->id ? 'selected' : '' }}
                                                {{ $hasKader ? 'disabled' : '' }}>
                                                {{-- Tampilkan Nama Posyandu dan RW-nya --}}
                                                {{ $posyandu->nama_posyandu }} (RW {{ $posyandu->rws->nomor_rw ?? 'N/A' }})
                                                @if($hasKader) (Sudah ada Akun) @endif
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('posyandu_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    <small class="form-text text-muted">Satu Posyandu hanya boleh memiliki satu akun Kader.</small>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-success">Generate / Update Akun Kader</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Form Generate Akun RT --}}
            <div class="card card-info card-outline mt-4">
                <div class="card-header">
                    <h5 class="card-title">Generate Akun RT per RW</h5>
                </div>
                <form action="{{ route('admin_desa.user_management.generate_rts') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="rw_id_for_rt">Pilih RW</label>
                            <select name="rw_id_for_rt" class="form-control @error('rw_id_for_rt') is-invalid @enderror" id="rw_id_for_rt" required>
                                <option value="">-- Pilih RW --</option>
                                @forelse($rws as $rw)
                                    <option value="{{ $rw->id }}" {{ old('rw_id_for_rt') == $rw->id ? 'selected' : '' }}>
                                        RW {{ $rw->nomor_rw }} ({{ $rw->rts->count() }} RT terdaftar)
                                    </option>
                                @empty
                                    <option value="" disabled>Belum ada RW yang terdaftar.</option>
                                @endforelse
                            </select>
                            @error('rw_id_for_rt') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="jumlah_rt">Jumlah RT yang Akan Digenerate untuk RW ini</label>
                            <input type="number" name="jumlah_rt" class="form-control @error('jumlah_rt') is-invalid @enderror" id="jumlah_rt" value="{{ old('jumlah_rt', 0) }}" min="0" required>
                            @error('jumlah_rt') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            <small class="form-text text-muted">Akan membuat RT dari 01 sampai jumlah yang dimasukkan untuk RW yang dipilih.</small>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-info">Generate / Update Akun RT</button>
                    </div>
                </form>
            </div>

            @if (session('generated_accounts'))
                <div class="card mt-4">
                    <div class="card-header">
                        <h3 class="card-title">Akun yang Baru Digenerate/Diperbarui</h3>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Tipe</th>
                                    <th>Nomor</th>
                                    <th>Email</th>
                                    <th>Password Default</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach (session('generated_accounts') as $account)
                                    <tr>
                                        <td>{{ $account['tipe'] }}</td>
                                        <td>{{ $account['nomor'] }}</td>
                                        <td>{{ $account['email'] }}</td>
                                        <td>{{ $account['password'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
        <div class="card-footer">
            <a href="{{ route('admin_desa.user_management.index') }}" class="btn btn-secondary">Kembali ke Daftar Akun</a>
        </div>
    </div>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Logika untuk dropdown RT dinamis berdasarkan RW (di form generate RT)
            const rwIdForRtSelect = $('#rw_id_for_rt');
            const jumlahRtInput = $('#jumlah_rt');

            function updateRtCountForRw() {
                const selectedRwId = rwIdForRtSelect.val();
                if (selectedRwId) {
                    // Ambil jumlah RT yang sudah ada untuk RW yang dipilih
                    const selectedRwOption = rwIdForRtSelect.find('option:selected');
                    const rtCountText = selectedRwOption.text().match(/\((\d+) RT terdaftar\)/);
                    const currentRtCount = rtCountText ? parseInt(rtCountText[1]) : 0;
                    
                    jumlahRtInput.val(currentRtCount); // Set nilai default ke jumlah RT yang sudah ada
                } else {
                    jumlahRtInput.val(0);
                }
            }

            rwIdForRtSelect.on('change', updateRtCountForRw);
            updateRtCountForRw(); // Panggil saat halaman dimuat untuk inisialisasi awal
        });
    </script>
@endsection
