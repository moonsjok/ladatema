<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ env('APP_NAME') }} .::. @yield('page-title')</title>
    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('apple-icon-57x57.png') }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('apple-icon-60x60.png') }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('apple-icon-72x72.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('apple-icon-76x76.png') }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('apple-icon-114x114.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('apple-icon-120x120.png') }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('apple-icon-144x144.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('apple-icon-152x152.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-icon-180x180.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('android-icon-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicon-96x96.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ asset('ms-icon-144x144.png') }}">
    <meta name="theme-color" content="#ffffff">

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <!-- Livewire Styles -->
    @livewireStyles
    <!-- Livewire Scripts -->
    @livewireScripts
    {!! SEOMeta::generate() !!}
    {!! OpenGraph::generate() !!}
    {!! Twitter::generate() !!}
    @stack('styles')

</head>

<body style="width:100%; overflow-x:hidden !important;">

    @include('layouts.guest.shares.navbar')
    {{-- <main class="container py-4"> --}}
    @include('layouts.partials._alert_messages')

    <div class="container-fluid bg-cover d-flex align-items-center justify-content-center"
        style="
        background-image: url('{{ asset('assets/img/background-white.png') }}');
        height: calc(100vh - 140px);
        background-repeat:no-repeat;
        background-size:cover;
         ">
        <div class="col-md-5 shadow p-5 rounded text-center">

            <div class="text-center">
                <h1 class="fw-bold">
                    @yield('title')
                </h1>
            </div>
            <h2 class="text-center fw-bold"
                style="font-display: block; font-size:80px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif ">
                @yield('code')
            </h2>
            @yield('message')


            <div class="d-grid gap-2 mt-5">
                <a href="{{ url()->previous() }}" class="fw-bold btn-lg btn btn-primary m-2">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Retour') }}
                </a>
            </div>


        </div>

    </div>

    @include('layouts.partials._swal_alert_messages')
    @stack('script')
</body>

</html>
