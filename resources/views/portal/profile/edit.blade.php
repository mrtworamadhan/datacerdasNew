@extends('layouts.portal')

@section('title', 'Edit Profil')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            {{-- Bagian untuk update informasi profil --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('portal.profile.partials.update-profile-information-form')
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mt-4">
        <div class="card-body">
            {{-- Bagian untuk update kata sandi --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('portal.profile.partials.update-password-form')
                </div>
            </div>
        </div>
    </div>

    <!-- <div class="card shadow-sm mt-4">
        <div class="card-body">
            {{-- Bagian untuk hapus akun --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('portal.profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div> -->
@endsection