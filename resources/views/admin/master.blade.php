@extends('adminlte::page')

@section('title', 'TataDesa')
@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--bootstrap-5-theme .select2-selection {
    }
    .sidebar {
        background-color: #0d7e7eff !important;
        
        color: #ffffff !important;
    }
    .sidebar.text {
        color: #ffffff !important;
    }
    .nav-sidebar .nav-item>.nav-link.active {
        background-color: #10A8A8 !important;
        color: #ffffff !important;
    }

    .nav-sidebar .nav-item>.nav-link.active i {
        color: rgb(255, 255, 255) !important;
    }

    .preloader {
        background-color: rgba(0, 0, 0, 0.89) !important;
    }
</style>
@stop

@section('content_header')
<h1 class="m-0 text-dark">Dashboard</h1>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @yield('content_main')
            </div>
        </div>
    </div>
</div>
@stop