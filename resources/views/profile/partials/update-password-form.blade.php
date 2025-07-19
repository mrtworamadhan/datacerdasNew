<section>
    <header>
        <h4 class="text-dark font-weight-bold">
            {{ __('Perbarui Password') }}
        </h4>

        <p class="mt-1 text-muted">
            {{ __('Pastikan akun Anda menggunakan password yang panjang dan acak agar tetap aman.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-4"> {{-- Menggunakan mt-4 untuk spacing --}}
        @csrf
        @method('put')

        <div class="form-group"> {{-- Menggunakan form-group --}}
            <label for="current_password" class="form-label">{{ __('Password Saat Ini') }}</label>
            <input id="current_password" name="current_password" type="password" class="form-control" autocomplete="current-password" />
            @error('current_password')
                <span class="invalid-feedback d-block" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group"> {{-- Menggunakan form-group --}}
            <label for="password" class="form-label">{{ __('Password Baru') }}</label>
            <input id="password" name="password" type="password" class="form-control" autocomplete="new-password" />
            @error('password')
                <span class="invalid-feedback d-block" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group"> {{-- Menggunakan form-group --}}
            <label for="password_confirmation" class="form-label">{{ __('Konfirmasi Password') }}</label>
            <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password" />
            @error('password_confirmation')
                <span class="invalid-feedback d-block" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="d-flex align-items-center mt-4"> {{-- Menggunakan d-flex mt-4 --}}
            <button type="submit" class="btn btn-primary">
                {{ __('Simpan') }}
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-muted ml-3"
                >{{ __('Disimpan.') }}</p>
            @endif
        </div>
    </form>
</section>
