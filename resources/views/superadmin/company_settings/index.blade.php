@extends('admin.master')

@section('title', 'Pengaturan Perusahaan - Desa Cerdas')

@section('content_header')
    <h1 class="m-0 text-dark">Pengaturan Perusahaan</h1>
@stop

@section('content_main')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Kelola Informasi Global Perusahaan</h3>
        </div>
        <form action="{{ route('company-settings.update') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <p class="text-muted">Di sini Anda dapat mengelola informasi kontak, media sosial, dan URL penting yang digunakan di seluruh platform Desa Cerdas.</p>
                <hr>

                @foreach($settings as $setting)
                    <div class="form-group">
                        <label for="setting_{{ $setting->key }}">{{ $setting->description ?? ucfirst(str_replace('_', ' ', $setting->key)) }}</label>
                        @if (Str::contains($setting->key, '_url'))
                            <input type="url" name="setting_{{ $setting->key }}" class="form-control @error('setting_'.$setting->key) is-invalid @enderror" id="setting_{{ $setting->key }}" value="{{ old('setting_'.$setting->key, $setting->value) }}" placeholder="https://example.com/profile">
                        @elseif (Str::contains($setting->key, 'number'))
                            <input type="text" name="setting_{{ $setting->key }}" class="form-control @error('setting_'.$setting->key) is-invalid @enderror" id="setting_{{ $setting->key }}" value="{{ old('setting_'.$setting->key, $setting->value) }}" placeholder="Contoh: +6281234567890">
                        @elseif (Str::contains($setting->key, 'address') || Str::contains($setting->key, 'description'))
                            <textarea name="setting_{{ $setting->key }}" class="form-control @error('setting_'.$setting->key) is-invalid @enderror" id="setting_{{ $setting->key }}" rows="3">{{ old('setting_'.$setting->key, $setting->value) }}</textarea>
                        @else
                            <input type="text" name="setting_{{ $setting->key }}" class="form-control @error('setting_'.$setting->key) is-invalid @enderror" id="setting_{{ $setting->key }}" value="{{ old('setting_'.$setting->key, $setting->value) }}">
                        @endif
                        @error('setting_'.$setting->key) <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                @endforeach

            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
            </div>
        </form>
    </div>
@endsection
