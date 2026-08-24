@if(Auth::check())
    @php
        $user = Auth::user();
        
        // Initiales de l'utilisateur (façon Google)
        $initials = '';
        if (!empty($user->prenoms) && !empty($user->nom)) {
            $initials = mb_strtoupper(mb_substr(trim($user->prenoms), 0, 1) . mb_substr(trim($user->nom), 0, 1));
        } else {
            $initials = mb_strtoupper(mb_substr(trim($user->name), 0, 1));
        }

        $roleLabel = match($user->role) {
            'owner' => 'Administrateur',
            'dev' => 'Développeur',
            'employee' => 'Employé',
            default => 'Étudiant',
        };

        $roleBadgeClass = match($user->role) {
            'owner' => 'bg-danger-subtle text-danger border-danger-subtle',
            'dev' => 'bg-dark text-white border-dark',
            'employee' => 'bg-info-subtle text-info border-info-subtle',
            default => 'bg-primary-subtle text-primary border-primary-subtle',
        };

        $avatarBgClass = match($user->role) {
            'owner' => 'bg-danger text-white',
            'dev' => 'bg-dark text-white',
            'employee' => 'bg-info text-white',
            default => 'bg-primary text-white',
        };
    @endphp

    @if(isset($mode) && $mode === 'sidebar')
        <!-- Google Dashboard Style Sidebar Profile Widget -->
        <div class="p-3 mb-3 bg-body-tertiary rounded-4 border border-light-subtle shadow-xs">
            <div class="d-flex align-items-center mb-2">
                <div class="rounded-circle {{ $avatarBgClass }} d-flex align-items-center justify-content-center me-3 flex-shrink-0 shadow-sm fw-bold fs-6" style="width: 44px; height: 44px;">
                    {{ $initials }}
                </div>
                <div class="overflow-hidden flex-grow-1">
                    <div class="fw-bold text-dark text-truncate fs-6 lh-sm" title="{{ $user->name }}">{{ $user->name }}</div>
                    <div class="text-muted text-truncate" style="font-size: 0.75rem;" title="{{ $user->email }}">{{ $user->email }}</div>
                </div>
            </div>
            <div class="pt-1">
                <span class="badge {{ $roleBadgeClass }} border rounded-pill px-2.5 py-1" style="font-size: 0.68rem; font-weight: 600;">
                    <i class="bi bi-shield-check me-1"></i>{{ $roleLabel }}
                </span>
            </div>
        </div>
    @else
        <!-- Google Topbar Icon + Dropdown Menu -->
        <div class="dropdown d-inline-block">
            <button class="btn p-0 border-0 bg-transparent" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="{{ $user->name }} ({{ $roleLabel }})">
                <div class="rounded-circle {{ $avatarBgClass }} d-flex align-items-center justify-content-center shadow-sm fw-bold border border-2 border-white" style="width: 38px; height: 38px; font-size: 0.9rem; transition: transform 0.2s ease;">
                    {{ $initials }}
                </div>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-3 mt-2" style="min-width: 260px; z-index: 1050;">
                <!-- Ligne 1 : Icone & Nom -->
                <li class="d-flex align-items-center mb-2 pb-2 border-bottom">
                    <div class="rounded-circle {{ $avatarBgClass }} d-flex align-items-center justify-content-center me-3 flex-shrink-0 shadow-sm fw-bold fs-5" style="width: 48px; height: 48px;">
                        {{ $initials }}
                    </div>
                    <div class="overflow-hidden">
                        <div class="fw-bold text-dark fs-6 lh-sm text-truncate" title="{{ $user->name }}">{{ $user->name }}</div>
                        <span class="badge {{ $roleBadgeClass }} border rounded-pill px-2 py-0.5 mt-1" style="font-size: 0.65rem;">
                            {{ $roleLabel }}
                        </span>
                    </div>
                </li>

                <!-- Ligne 2 : Email -->
                <li class="py-1">
                    <div class="text-muted small">
                        <i class="bi bi-envelope me-2 text-primary"></i>
                        <span class="text-dark fw-medium text-break">{{ $user->email }}</span>
                    </div>
                </li>

                <!-- Ligne 3 : Rôle -->
                <li class="py-1">
                    <div class="text-muted small">
                        <i class="bi bi-person-badge me-2 text-primary"></i>
                        <span class="text-dark fw-medium">{{ $roleLabel }}</span>
                    </div>
                </li>

                @if(!empty($user->phone_call) || !empty($user->phone_whatsapp))
                <!-- Ligne 4 : Téléphone s'il existe -->
                <li class="py-1">
                    <div class="text-muted small">
                        <i class="bi bi-telephone me-2 text-primary"></i>
                        <span class="text-dark fw-medium">{{ $user->phone_call ?? $user->phone_whatsapp }}</span>
                    </div>
                </li>
                @endif

                <li><hr class="dropdown-divider my-2"></li>

                <!-- Ligne Actions -->
                <li>
                    <a class="dropdown-item rounded-3 py-2 px-3 small d-flex align-items-center" href="{{ route('profile.complete') }}">
                        <i class="bi bi-gear me-2 text-secondary"></i> Gérer mon profil
                    </a>
                </li>
                <li>
                    <a class="dropdown-item rounded-3 py-2 px-3 small d-flex align-items-center text-danger fw-semibold" href="{{ route('logout') }}"
                       onclick="event.preventDefault(); document.getElementById('topbar-logout-form').submit();">
                        <i class="bi bi-box-arrow-right me-2"></i> Déconnexion
                    </a>
                    <form id="topbar-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li>
            </ul>
        </div>
    @endif
@endif
