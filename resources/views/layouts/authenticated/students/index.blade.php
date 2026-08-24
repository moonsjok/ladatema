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
        @if(app()->environment('local'))
            <!-- Développement : utiliser HTTP localhost -->
            <script type="module" src="http://localhost:5173/@vite/client"></script>
            <script type="module" src="http://localhost:5173/resources/js/app.js"></script>
            <link rel="stylesheet" href="http://localhost:5173/resources/css/app.css">
        @else
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
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

            @include('layouts.authenticated.students.shares.navbar')

            <!-- Contenu principal -->
            <main class="col-md-10 ">
                <div
                    class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-0 border-bottom">

                    <div class="dashboard-page-header w-100">
                        <span class="d-none d-sm-block w-100">
                            <div class="container-fluid p-0 mt-1">
                                <div class="row align-items-center">
                                    <div class="col text-start">
                                        <h4 class="mb-0 fw-bold text-dark">@yield('page-title')</h4>
                                    </div>
                                    <div class="col-auto d-flex justify-content-end align-items-center">
                                        @include('layouts.partials.user_account_badge')
                                        @livewire('authenticated.header-notification-bell')
                                        <a class="text-danger fw-bold mb-0 ms-2" href="{{ route('logout') }}"
                                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();" title="Déconnexion">
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
                            <div class="d-flex align-items-center justify-content-between py-1">
                                <div>
                                    @include('layouts.shares.logo')
                                </div>
                                <div class="ms-2">
                                    @include('layouts.partials.user_account_badge')
                                </div>
                            </div>
                        </span>
                    </div>

                    <button style="margin-top:-70px;" class="btn btn-primary ms-auto d-md-none" type="button"
                        data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar" aria-controls="offcanvasSidebar">
                        <i class="bi bi-list"></i>
                    </button>

                </div>
                <div class="container-fluid m-0 p-0">
                    <div class="row main-content m-0 p-0">
                        <div class="col-12 min-vh-100 m-0 p-0">
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
        <strong>&copy {{ env('APP_NAME') }}</strong>
        <br />
        <p>Developpé par Moon's Jok Corp - <a href="https://moonsjokcorp.com" class="">Visitez notre site</a>
    </footer>
    @include('layouts.partials._swal_alert_messages')
    @include('layouts.partials.whatsapp-contact')

</body>

</html>
