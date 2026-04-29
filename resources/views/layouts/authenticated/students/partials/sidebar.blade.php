<ul class="nav flex-column">

    <!-- Auth user name -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active-link' : '' }}" href="{{ route('dashboard') }}">
            <i class="bi bi-speedometer2"></i> Tableau de bord
        </a>
    </li>

    <!-- Formation -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('formations.*') ? 'active-link' : '' }} toggle-menu"
            href="{{ route('guest.formationsList') }}">
            <i class="bi bi-mortarboard-fill"></i> Formations
        </a>
    </li>

    <!-- Évaluations -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('student.evaluations.*') ? 'active-link' : '' }} toggle-menu"
            href="{{ route('student.evaluations.index') }}">
            <i class="bi bi-check-circle"></i> Évaluations
        </a>
    </li>

    <!-- Bouton de déconnexion -->
    <div class="mt-auto">
        <a class="nav-item nav-link logout " href="{{ route('logout') }}"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="bi bi-box-arrow-right"></i> Déconnexion
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
</ul>
