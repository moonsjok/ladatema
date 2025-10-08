@extends('layouts.authenticated.owners.index')

@section('page-title', 'Tableau de bord')

@section('dashboard-content')

    <div class="container-fluid mt-3">
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
