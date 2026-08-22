@extends('layouts.authenticated.owners.index')

@section('page-title', 'Tableau de bord')

@section('dashboard-content')

    <div class="container-fluid mt-3">
        @if(isset($topNotifications) && $topNotifications->count() > 0)
            <div class="row px-2 mb-3">
                <div class="col-12">
                    @foreach($topNotifications as $topNotif)
                        <div class="alert alert-{{ $topNotif->is_important ? 'danger' : 'info' }} alert-dismissible fade show shadow-sm rounded-4 border-0 p-3 mb-2" id="notif-banner-owner-{{ $topNotif->id }}" role="alert">
                            <div class="d-flex align-items-start">
                                <div class="me-3 fs-3 text-{{ $topNotif->is_important ? 'danger' : 'info' }}">
                                    <i class="bi {{ $topNotif->is_important ? 'bi-exclamation-triangle-fill' : 'bi-bell-fill' }}"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-1">
                                        <h5 class="alert-heading fw-bold mb-0 me-2">{{ $topNotif->title }}</h5>
                                        @if($topNotif->is_important)
                                            <span class="badge bg-danger rounded-pill">Important</span>
                                        @endif
                                    </div>
                                    <p class="mb-0 text-dark" style="white-space: pre-line;">{{ $topNotif->message }}</p>
                                    <small class="text-muted d-block mt-1" style="font-size: 0.8rem;">
                                        <i class="bi bi-clock me-1"></i> De : {{ $topNotif->sender->name ?? 'Développeur' }} ({{ $topNotif->created_at->diffForHumans() }})
                                    </small>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill ms-2" onclick="markOwnerNotificationRead({{ $topNotif->id }})" title="Marquer comme lu">
                                        <i class="bi bi-check-lg me-1"></i> Marquer comme lu
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <script>
                function markOwnerNotificationRead(notifId) {
                    fetch('/app-notifications/' + notifId + '/read', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    }).then(response => {
                        if (response.ok) {
                            const elem = document.getElementById('notif-banner-owner-' + notifId);
                            if (elem) {
                                elem.classList.remove('show');
                                setTimeout(() => elem.remove(), 300);
                            }
                        }
                    }).catch(err => console.error(err));
                }
            </script>
        @endif

        <div class="row g-3">

            <!-- Statistiques globales -->
            <div class="col-md-3">
                <a href="{{ route('subscriptions.students') }}" tabindex="0" id="card-students"
                    class="text-decoration-none card shadow-sm border-0">
                    <div class="card-body text-center">
                        <i class="bi bi-people-fill fs-1 text-primary"></i>
                        <h5 class="card-title mt-2">Apprenant(s)</h5>
                        <p class="fs-4 fw-bold">{{ $totalStudents ?? 0 }}</p>
                    </div>
                </a>
            </div>

            <div class="col-md-3">
                <a href="{{ route('formations.index') }}" tabindex="0" id="card-formations"
                    class="text-decoration-none card shadow-sm border-0">
                    <div class="card-body text-center">
                        <i class="bi bi-mortarboard-fill fs-1 text-success"></i>
                        <h5 class="card-title mt-2">Formation(s)</h5>
                        <p class="fs-4 fw-bold">{{ $totalFormations ?? 0 }}</p>
                    </div>
                </a>
            </div>

            <div class="col-md-3">
                <a href="{{ route('courses.index') }}" tabindex="0" id="card-courses"
                    class="text-decoration-none card shadow-sm border-0">
                    <div class="card-body text-center">
                        <i class="bi bi-journal-text fs-1 text-warning"></i>
                        <h5 class="card-title mt-2">Cour(s)</h5>
                        <p class="fs-4 fw-bold">{{ $totalCourses ?? 0 }}</p>
                    </div>
                </a>
            </div>

            <div class="col-md-3">
                <a href="{{ route('subscriptions.overview') }}" tabindex="0" id="card-subscriptions"
                    class="text-decoration-none card shadow-sm border-0">
                    <div class="card-body text-center">
                        <i class="bi bi-card-checklist fs-1 text-danger"></i>
                        <h5 class="card-title mt-2">Souscription(s)</h5>
                        <p class="fs-4 fw-bold">{{ $totalSubscriptions ?? 0 }}</p>
                        @if ($totalPendingSubscriptions > 0)
                            <p class="fs-4 fw-bold">{{ $totalSubscriptions ?? 0 }}</p>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ $totalPendingSubscriptions }}
                                <span class="visually-hidden">Peding Subscriptions</span>
                            </span>
                        @endif
                    </div>
                </a>
            </div>
        </div>

        <!-- Liste des formations récentes -->
        <div class="row mt-4">
            <a href="{{ route('formations.index') }}" class=" text-decoration-none col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <i class="bi bi-book"></i> Formations récentes
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @forelse($latestFormations as $formation)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $formation->title }}
                                    <span class="badge bg-secondary">{{ $formation->courses_count }} cour(s)</span>
                                </li>
                            @empty
                                <li class="list-group-item text-muted">Aucune formation disponible</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </a>

            <!-- Liste des derniers utilisateurs inscrits -->
            <a href="{{ route('subscriptions.students') }}" class="col-md-6  text-decoration-none">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <i class="bi bi-person-plus-fill"></i> Nouveaux utilisateurs
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @forelse($latestUsers as $user)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $user->name }} .::. {{ __($user->role) }}
                                    <span class="text-muted">{{ $user->created_at->diffForHumans() }}</span>
                                </li>
                            @empty
                                <li class="list-group-item text-muted">Aucun nouvel utilisateur</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </a>
        </div>

    </div>
    @push('styles')
        <style>
            .card:hover,
            .card:focus {
                background-color: rgba(0, 0, 0, 0.05);
                transform: translateY(-3px);
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }

            .card:focus {
                outline: none;
                box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.5);
            }

            #card-students:hover .card-body,
            #card-students:focus .card-body {
                background-color: rgba(0, 123, 255, 0.1);
                /* Exemple de couleur spécifique pour étudiants */
            }

            #card-formations:hover .card-body,
            #card-formations:focus .card-body {
                background-color: rgba(40, 167, 69, 0.1);
                /* Exemple de couleur pour formations */
            }

            #card-courses:hover .card-body,
            #card-courses:focus .card-body {
                background-color: rgba(255, 193, 7, 0.1);
                /* Exemple de couleur pour cours */
            }

            #card-subscriptions:hover .card-body,
            #card-subscriptions:focus .card-body {
                background-color: rgba(220, 53, 69, 0.1);
                /* Exemple de couleur pour souscriptions */
            }
        </style>
    @endpush
@endsection
