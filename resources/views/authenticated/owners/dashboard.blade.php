@extends('layouts.authenticated.owners.index')

@section('page-title', 'Tableau de bord')

@section('dashboard-content')

    <div class="container-fluid mt-3">
        @livewire('authenticated.top-notifications')

        @if(isset($pendingSubscriptionsList) && $pendingSubscriptionsList->count() > 0)
            <!-- Section alerte : Souscriptions en attente de validation -->
            <div class="row my-3">
                <div class="col-12">
                    <div class="card shadow-sm border-danger border-opacity-50 rounded-4 overflow-hidden">
                        <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center py-3">
                            <h5 class="mb-0 fw-bold fs-6">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                Souscriptions en attente de validation ({{ $pendingSubscriptionsList->count() }})
                            </h5>
                            <a href="{{ route('subscriptions.index') }}" class="btn btn-sm btn-light text-danger fw-semibold rounded-pill px-3">
                                Ouvrir le gestionnaire <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-3">Apprenant / Utilisateur</th>
                                            <th>Contenu Demandé</th>
                                            <th>Référence & Prix</th>
                                            <th>Date de demande</th>
                                            <th class="text-end pe-3">Vérification & Validation</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pendingSubscriptionsList as $pendingSub)
                                            <tr>
                                                <td class="ps-3">
                                                    <div class="fw-bold text-dark">{{ $pendingSub->user->name ?? 'N/A' }}</div>
                                                    <div class="text-muted small">{{ $pendingSub->user->email ?? 'N/A' }}</div>
                                                    <div class="mt-1">
                                                        @if(!empty($pendingSub->user->phone_call))
                                                            <a href="tel:{{ $pendingSub->user->phone_call }}" class="btn btn-xs btn-outline-secondary py-0 px-2 rounded-pill me-1" title="Appeler">
                                                                <i class="bi bi-telephone me-1"></i>{{ $pendingSub->user->phone_call }}
                                                            </a>
                                                        @endif
                                                        @if(!empty($pendingSub->user->phone_whatsapp))
                                                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $pendingSub->user->phone_whatsapp) }}" target="_blank" class="btn btn-xs btn-outline-success py-0 px-2 rounded-pill" title="Envoyer un message WhatsApp">
                                                                <i class="bi bi-whatsapp me-1"></i>WhatsApp
                                                            </a>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary text-capitalize mb-1">{{ $pendingSub->type }}</span>
                                                    <div class="fw-semibold text-dark">
                                                        {{ $pendingSub->formation->title ?? ($pendingSub->course->title ?? ($pendingSub->chapter->title ?? 'N/A')) }}
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($pendingSub->payment_reference)
                                                        <div class="badge bg-light text-dark border border-secondary font-monospace fs-6 mb-1">
                                                            {{ $pendingSub->payment_reference }}
                                                        </div>
                                                    @else
                                                        <span class="badge bg-warning text-dark mb-1">Pas de référence</span>
                                                    @endif
                                                    <div class="fw-bold text-success">{{ number_format($pendingSub->price, 0, ',', ' ') }} FCFA</div>
                                                </td>
                                                <td>
                                                    <div class="small fw-semibold text-dark">{{ $pendingSub->created_at ? $pendingSub->created_at->diffForHumans() : 'N/A' }}</div>
                                                    <div class="text-muted" style="font-size: 0.75rem;">{{ $pendingSub->created_at ? $pendingSub->created_at->format('d/m/Y H:i') : '' }}</div>
                                                </td>
                                                <td class="text-end pe-3">
                                                    <form action="{{ route('subscriptions.validate', $pendingSub->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Confirmez-vous le paiement et la validation des accès pour {{ e($pendingSub->user->name ?? 'cet étudiant') }} ?');">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-success btn-sm px-3 rounded-pill fw-semibold shadow-xs">
                                                            <i class="bi bi-check-circle-fill me-1"></i> Valider l'accès
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="row g-3">

            <!-- Statistiques globales -->
            <div class="col-md-3">
                <a href="{{ route('subscriptions.index') }}" tabindex="0" id="card-students"
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
                <a href="{{ route('subscriptions.index') }}" tabindex="0" id="card-subscriptions"
                    class="text-decoration-none card shadow-sm border-0">
                    <div class="card-body text-center">
                        <i class="bi bi-card-checklist fs-1 text-danger"></i>
                        <h5 class="card-title mt-2">Souscription(s)</h5>
                        <p class="fs-4 fw-bold">{{ $totalSubscriptions ?? 0 }}</p>
                        @if ($totalPendingSubscriptions > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ $totalPendingSubscriptions }}
                                <span class="visually-hidden">Pending Subscriptions</span>
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
            <a href="{{ route('subscriptions.index') }}" class="col-md-6  text-decoration-none">
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
