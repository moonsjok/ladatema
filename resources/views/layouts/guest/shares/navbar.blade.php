<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid d-flex align-items-center justify-content-between">
        <button class="navbar-toggler me-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu"
            aria-controls="navbarMenu" aria-expanded="false" aria-label="Toggle navigation">
            <i class="bi bi-list fs-3"></i>
        </button>

        <a class="navbar-brand d-flex align-items-center" href="{{ route('welcome') }}">
            @include('layouts.shares.logo')
        </a>

        <div class="collapse navbar-collapse justify-content-center" id="navbarMenu">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="{{ route('welcome') }}">
                        <i class="bi bi-house-door-fill"></i> Accueil
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="{{ route('nos.services') }}">
                        <i class="bi bi-gear-fill"></i> Nos services
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('guest.formationsList') }}">
                        <i class="bi bi-mortarboard-fill"></i> Formations
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('contact.form') }}">
                        <i class="bi bi-telephone-fill"></i> Contacts
                    </a>
                </li>
            </ul>
        </div>

        <div class="d-flex align-items-center">
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-outline-primary btn-sm me-2 d-flex align-items-center">
                    <i class="bi bi-speedometer2"></i>
                    <span class="d-none d-lg-inline ms-1">Tableau de bord</span>
                </a>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm d-flex align-items-center">
                        <i class="bi bi-box-arrow-right"></i>
                        <span class="d-none d-lg-inline ms-1">Déconnexion</span>
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm me-2 d-flex align-items-center">
                    <i class="bi bi-box-arrow-in-right"></i>
                    <span class="d-none d-lg-inline ms-1">Connexion</span>
                </a>
                <a href="{{ route('register') }}" class="btn btn-outline-success btn-sm d-flex align-items-center">
                    <i class="bi bi-person-plus-fill"></i>
                    <span class="d-none d-lg-inline ms-1">Inscription</span>
                </a>
            @endauth
        </div>
    </div>
</nav>
