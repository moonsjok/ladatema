@if(Auth::check())
    @php
        $user = Auth::user();
        $roleLabel = match($user->role) {
            'owner' => 'Administrateur',
            'dev' => 'Développeur',
            'employee' => 'Employé',
            default => 'Étudiant',
        };
        $roleBadgeClass = match($user->role) {
            'owner' => 'bg-danger-subtle text-danger border-danger',
            'dev' => 'bg-dark text-white border-dark',
            'employee' => 'bg-info-subtle text-info border-info',
            default => 'bg-primary-subtle text-primary border-primary',
        };
    @endphp

    @if(isset($mode) && $mode === 'sidebar')
        <!-- Mode Sidebar Card -->
        <div class="user-identity-card p-3 mb-3 bg-white rounded-3 shadow-xs border text-start">
            <div class="d-flex align-items-center">
                <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-2 flex-shrink-0" style="width: 40px; height: 40px;">
                    <i class="bi bi-person-fill fs-5"></i>
                </div>
                <div class="overflow-hidden flex-grow-1">
                    <div class="fw-bold text-dark text-truncate small" title="{{ $user->name }}">{{ $user->name }}</div>
                    <div class="text-muted text-truncate" style="font-size: 0.73rem;" title="{{ $user->email }}">{{ $user->email }}</div>
                    <span class="badge {{ $roleBadgeClass }} border rounded-pill px-2 py-0.5 mt-1" style="font-size: 0.65rem;">
                        <i class="bi bi-shield-check me-1"></i>{{ $roleLabel }}
                    </span>
                </div>
            </div>
        </div>
    @else
        <!-- Mode Topbar Badge (Header) -->
        <div class="d-inline-flex align-items-center bg-white border rounded-pill px-3 py-1.5 shadow-xs me-2">
            <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-2" style="width: 28px; height: 28px;">
                <i class="bi bi-person-fill small"></i>
            </div>
            <div class="lh-1 text-start me-2 d-none d-md-block">
                <div class="fw-bold text-dark small mb-0.5 text-truncate" style="max-width: 140px;" title="{{ $user->name }}">{{ $user->name }}</div>
                <div class="text-muted text-truncate" style="font-size: 0.7rem; max-width: 140px;" title="{{ $user->email }}">{{ $user->email }}</div>
            </div>
            <span class="badge {{ $roleBadgeClass }} border rounded-pill px-2 py-0.5 text-uppercase" style="font-size: 0.65rem;">
                {{ $roleLabel }}
            </span>
        </div>
    @endif
@endif
