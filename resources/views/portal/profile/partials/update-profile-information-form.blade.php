<section>
    <header>
        <h4 class="text-dark font-weight-bold">
            {{ __('Informasi Profil') }}
        </h4>

        <p class="mt-1 text-muted">
            {{ __("Perbarui informasi profil dan alamat email akun Anda.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('portal.profile.update', ['subdomain' => $subdomain]) }}" class="mt-4">
        @csrf
        @method('patch')
        <div class="form-group"> {{-- Menggunakan form-group --}}
            <label for="name" class="form-label">{{ __('Nama') }}</label>
            <input id="name" name="name" type="text" class="form-control" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
            @error('name')
                <span class="invalid-feedback d-block" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group"> {{-- Menggunakan form-group --}}
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input id="email" name="email" type="email" class="form-control" value="{{ old('email', $user->email) }}" required autocomplete="username" />
            @error('email')
                <span class="invalid-feedback d-block" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror

            @if ($user instanceof \Illuminate\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Alamat email Anda belum terverifikasi.') }}

                        <button form="send-verification" class="btn btn-link p-0 m-0 align-baseline text-sm text-muted text-decoration-underline">
                            {{ __('Klik di sini untuk mengirim ulang email verifikasi.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <div class="alert alert-success mt-2">
                            {{ __('Tautan verifikasi baru telah dikirim ke alamat email yang Anda berikan.') }}
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <div class="d-flex align-items-center mt-4"> {{-- Menggunakan d-flex mt-4 --}}
            <button type="submit" class="btn btn-primary">
                {{ __('Simpan') }}
            </button>

            @if (session('status') === 'profile-updated')
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
