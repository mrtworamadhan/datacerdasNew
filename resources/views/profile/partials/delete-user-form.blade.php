<section class="mb-4"> {{-- Menggunakan mb-4 untuk spacing --}}
    <header>
        <h4 class="text-dark font-weight-bold"> {{-- Menggunakan h4 untuk heading --}}
            {{ __('Hapus Akun') }}
        </h4>

        <p class="mt-1 text-muted"> {{-- Menggunakan text-muted untuk deskripsi --}}
            {{ __('Setelah akun Anda dihapus, semua sumber daya dan data akan dihapus secara permanen. Sebelum menghapus akun Anda, harap unduh data atau informasi apa pun yang ingin Anda simpan.') }}
        </p>
    </header>

    {{-- Tombol untuk memicu modal Bootstrap --}}
    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#confirmUserDeletionModal">
        {{ __('Hapus Akun') }}
    </button>

    {{-- Modal Konfirmasi Hapus Akun (Bootstrap Modal) --}}
    <div class="modal fade" id="confirmUserDeletionModal" tabindex="-1" role="dialog" aria-labelledby="confirmUserDeletionModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')

                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmUserDeletionModalLabel">{{ __('Apakah Anda yakin ingin menghapus akun Anda?') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <p class="text-muted">
                            {{ __('Setelah akun Anda dihapus, semua sumber daya dan data akan dihapus secara permanen. Harap masukkan password Anda untuk mengonfirmasi bahwa Anda ingin menghapus akun Anda secara permanen.') }}
                        </p>

                        <div class="form-group mt-3"> {{-- Menggunakan form-group untuk input --}}
                            <label for="password" class="sr-only">{{ __('Password') }}</label>
                            <input id="password" name="password" type="password" class="form-control" placeholder="{{ __('Password') }}" />
                            @error('password', 'userDeletion') {{-- Menargetkan error bag 'userDeletion' --}}
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="modal-footer d-flex justify-content-end"> {{-- Menggunakan d-flex justify-content-end --}}
                        <button type="button" class="btn btn-secondary mr-2" data-dismiss="modal"> {{-- Menggunakan mr-2 untuk margin --}}
                            {{ __('Batal') }}
                        </button>

                        <button type="submit" class="btn btn-danger">
                            {{ __('Hapus Akun') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
