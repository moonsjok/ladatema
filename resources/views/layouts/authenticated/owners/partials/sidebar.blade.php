<ul class="nav flex-column">

    <!-- Auth user name -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active-link' : '' }}" href="{{ route('dashboard') }}">
            <i class="bi bi-speedometer2"></i> Tableau de bord
        </a>
    </li>

    <!-- Section Catégorie -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('categories.*') ? 'active-link' : '' }} toggle-menu"
            href="{{ route('categories.index') }}">
            <i class="bi bi-grid"></i> Catégories
        </a>
    </li>

    <!-- Section Sous-catégorie -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('subcategories.*') ? 'active-link' : '' }} toggle-menu"
            href="{{ route('subcategories.index') }}">
            <i class="bi bi-diagram-3"></i> Sous-catégories
        </a>
    </li>

    <!-- Formations -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('formations.*') ? 'active-link' : '' }} toggle-menu"
            href="{{ route('formations.index') }}">
            <i class="bi bi-mortarboard-fill"></i> Formations
        </a>
    </li>

    <!-- Cours -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('courses.*') ? 'active-link' : '' }} toggle-menu"
            href="{{ route('courses.index') }}">
            <i class="bi bi-journal-text"></i> Cours
        </a>
    </li>

    <!-- Chapitres -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('chapters.*') ? 'active-link' : '' }} toggle-menu"
            href="{{ route('chapters.index') }}">
            <i class="bi bi-book-half"></i> Chapitres
        </a>
    </li>

    <!-- Évaluations -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('evaluations.*') ? 'active-link' : '' }} toggle-menu"
            href="{{ route('evaluations.index') }}">
            <i class="bi bi-check-circle"></i> Évaluations
        </a>
    </li>

    <!-- Partenaires -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('partners.*') ? 'active-link' : '' }} toggle-menu"
            href="{{ route('partners.index') }}">
            <i class="bi bi-people-fill"></i> Partenaires
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
