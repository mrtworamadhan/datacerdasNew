@extends('admin.master')

@section('title', 'Edit Profil Saya - Desa Cerdas')

@section('content_header')
    <h1 class="m-0 text-dark">Edit Profil Saya</h1>
@stop

@section('content_main')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg rounded-xl card-shadow">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg rounded-xl card-shadow">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg rounded-xl card-shadow">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    {{-- Tambahkan CSS kustom Anda di sini jika diperlukan --}}
    <style>
        .card-shadow {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08);
        }
        .form-input {
            border-radius: 0.5rem;
            border: 1px solid #D1D5DB;
            padding: 0.75rem 1rem;
            width: 100%;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        .form-input:focus {
            border-color: #10A8A8;
            box-shadow: 0 0 0 3px rgba(16, 168, 168, 0.25);
            outline: none;
        }
    </style>
@endpush
