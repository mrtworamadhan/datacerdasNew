@extends('layouts.anjungan')

@section('content')
    <p class="login-box-msg" style="font-size: 1.2rem;">Selamat Datang! Silakan masukkan Nomor Induk Kependudukan (NIK) Anda untuk memulai.</p>

    <form action="{{ route('anjungan.verifikasi') }}" method="POST">
        @csrf
        <div class="input-group mb-3">
            <input type="number" name="nik" class="form-control form-control-lg" placeholder="Ketik 16 Digit NIK Anda..." required autofocus>
            <div class="input-group-append">
                <div class="input-group-text"><span class="fas fa-id-card"></span></div>
            </div>
        </div>
        @if ($errors->any())
            <div class="alert alert-danger py-2">
                {{ $errors->first() }}
            </div>
        @endif
        <div class="row mt-4">
            <div class="col-12">
                <button type="submit" class="btn btn-primary btn-block btn-lg">
                    <i class="fas fa-arrow-right mr-2"></i> Lanjutkan
                </button>
            </div>
        </div>
    </form>
@stop