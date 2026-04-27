@extends('layouts.base')

@section('title', $title)

@section('body-class', 'd-flex flex-column')

@section('content')
    <div class="page page-center">
        <div class="container container-normal py-4">
            <div class="row align-items-center g-4">
                <div class="col-lg">
                    <div class="container-tight">
                        <div class="text-center mb-3">
                            <a href="{{ route('login') }}" class="navbar-brand navbar-brand-autodark">
                                <img style="width: 290px" src="{{ asset('static/ryoogen/logo/DARK.png') }}"
                                    alt="logo-aplikasi">
                            </a>
                        </div>

                        <div>
                            @if ($errors->any())
                                <div class="bg-white alert alert-warning alert-dismissible" role="alert">
                                    <div class="d-flex ">
                                        <div class="me-3">
                                            <h1 class="text-warning las la-exclamation-triangle"></h1>
                                        </div>
                                        <div>
                                            <h4 class="alert-title">Ada yang salah.</h4>
                                            <div class="text-muted">
                                                @foreach ($errors->all() as $error)
                                                    <p> - {{ $error }}</p>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                                </div>
                            @endif
                        </div>

                        {{ $slot }}

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
