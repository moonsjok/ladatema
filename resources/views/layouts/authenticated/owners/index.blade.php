<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- <title>{{ config('app.name', 'Laravel') }} - {{ $title ?? 'Dashboard' }}</title> --}}
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
    <script src="{{ asset('vendor/jquery-3.6.0.min.js') }}"></script>
    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <!-- Livewire Styles -->
    @livewireStyles
    <!-- Livewire Scripts -->
    @livewireScripts
    @include('sweetalert::alert')
    <!-- Ici, vous stack les styles supplémentaires -->
    @stack('styles')
    @stack('scripts')
</head>

<body class="bg-light">
    <div class="container-fluid">
        <div class="row">

            @include('layouts.authenticated.owners.shares.navbar')

            <!-- Contenu principal -->
            <main class="col-md-10 ">
                <div
                    class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-0 border-bottom">

                    <div class="dashboard-page-header ">
                        <span class="d-none d-sm-block ">
                            <div class="container d-none d-sm-block ml-5 mt-3">
                                <div class="row">
                                    <div class="col-10 text-center">
                                        @yield('page-title')
                                    </div>
                                    <div class="col-2 d-flex justify-content-end align-items-stretch p-0">
                                        <a class=" text-danger fw-bold mb-3 " href="{{ route('logout') }}"
                                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <i class="bi bi-box-arrow-right"></i>
                                        </a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                            style="display: none;">
                                            @csrf
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </span>

                        <span class="d-block d-sm-none">
                            <h1>
                                @include('layouts.shares.logo')
                            </h1>
                        </span>
                    </div>

                    <button style="margin-top:-70px;" class="btn btn-primary ms-auto d-md-none" type="button"
                        data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar" aria-controls="offcanvasSidebar">
                        <i class="bi bi-list"></i>
                    </button>

                </div>
                <div class="container-fluid m-0 p-0">
                    <div class="row main-content m-0 p-0">
                        <div class="col-12 min-vh-100 m-0 p-3">

                            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary my-3">
                                <i class="bi bi-arrow-left me-2"></i> Retour
                            </a>
                            @include('layouts.partials._alert_messages')
                            <!-- Contenu spécifique -->
                            @yield('dashboard-content')
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <footer class="footer">
        <strong>&copy; {{ env('APP_NAME') }}</strong> Version {{ env('APP_VERSION') }} .::. Laravel
        v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})
        <br />
        <i>Developpé par Moon's Jok Corp - <a href="https://moonsjokcorp.com" class="">Visitez notre site</a>
    </footer>
    @include('layouts.partials._swal_alert_messages')

</body>

</html>
