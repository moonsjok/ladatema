@extends('layouts.authenticated.students.index')
@section('page-title', 'Tableau de bord')
@section('dashboard-content')

    <div class="container-fluid mt-2">
        @livewire('authenticated.top-notifications')

        <div class="row">
            <!-- Colonne Principale : Mes Formations & Souscriptions -->
            <div class="col-md-8 p-3">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold mb-0">
                        <i class="bi bi-mortarboard-fill text-primary me-2"></i> Mes Formations
                    </h2>
<a href="{{ route('guest.formationsList') }}"
   class="btn btn-sm btn-outline-primary fw-semibold">
    <i class="bi bi-plus-circle me-1"></i>
    <span class="d-none d-sm-inline">Nouvelle souscription</span>
</a>
                </div>

                @if ($formations->isEmpty() && (!isset($pendingSubscriptions) || $pendingSubscriptions->isEmpty()) && (!isset($expiredSubscriptions) || $expiredSubscriptions->isEmpty()))
                    <div class="card border-0 shadow-sm rounded-4 p-4 text-center">
                        <div class="mb-3">
                            <i class="bi bi-journal-album display-1 text-primary"></i>
                        </div>
                        <h4 class="fw-bold">Explorez nos parcours !</h4>
                        <p class="text-muted">
                            Découvrez une nouvelle façon d'apprendre avec <strong>{{ env('APP_NAME') }}</strong> !
                            Accédez à des formations interactives et adaptées à votre rythme, conçues pour booster vos compétences.
                        </p>
                        <div>
                            <a href="{{ route('guest.formationsList') }}" class="btn btn-primary btn-lg px-4 rounded-pill">
                                <i class="bi bi-arrow-right-circle me-1"></i> Parcourir le catalogue
                            </a>
                        </div>
                    </div>
                @else
                    <!-- Section 1 : Souscriptions Actives -->
                    @if(!$formations->isEmpty())
                        @php
                            $isSingleFormation = ($formations->count() === 1);
                        @endphp
                        <div class="mb-4">
                            <h5 class="fw-bold text-success mb-3">
                                <i class="bi bi-check-circle-fill me-2"></i> Souscriptions en cours (Actives)
                            </h5>
                            <div class="accordion shadow-sm rounded-3 overflow-hidden" id="formationsAccordion">
                                @foreach ($formations as $formation)
                                    @if ($formation)
                                        @php
                                            $sub = $souscriptions->firstWhere('formation_id', $formation->id);
                                            $isExpanded = $isSingleFormation;
                                        @endphp
                                        <div class="accordion-item border-0 border-bottom">
                                            <h2 class="accordion-header" id="heading{{ $formation->id }}">
                                                <button class="accordion-button {{ $isExpanded ? '' : 'collapsed' }} py-3" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapse{{ $formation->id }}"
                                                    aria-expanded="{{ $isExpanded ? 'true' : 'false' }}" aria-controls="collapse{{ $formation->id }}">
                                                    <div class="d-flex align-items-start w-100 me-3">
                                                        <i class="bi bi-book-fill text-primary fs-5 me-3 mt-1"></i>
                                                        <div class="flex-grow-1">
                                                            <div class="fw-bold text-dark fs-6 mb-1">
                                                                {{ $formation->title }}
                                                            </div>
                                                            @if($sub && $sub->expires_at)
                                                                <div class="small text-success fw-semibold mb-1">
                                                                    <i class="bi bi-clock me-1"></i>
                                                                    @if(is_string($sub->expires_at))
                                                                        Valide jusqu'au {{ \Carbon\Carbon::parse($sub->expires_at)->format('d/m/Y') }}
                                                                    @else
                                                                        Valide jusqu'au {{ $sub->expires_at->format('d/m/Y') }}
                                                                    @endif
                                                                </div>
                                                                <div class="small text-muted fw-bold">
                                                                    <i class="bi bi-hourglass-split me-1"></i> Reste: {{ $sub->days_remaining }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="collapse{{ $formation->id }}" class="accordion-collapse collapse {{ $isExpanded ? 'show' : '' }}"
                                                aria-labelledby="heading{{ $formation->id }}"
                                                data-bs-parent="#formationsAccordion">
                                                <div class="accordion-body bg-light">
                                                    <p class="text-justify text-secondary mb-3">{!! $formation->description !!}</p>

                                                    <h6 class="fw-bold mb-3"><i class="bi bi-journal-text text-primary me-2"></i> Liste des cours disponibles :</h6>
                                                    <div class="list-group shadow-sm rounded-3">
                                                        @php
                                                            $isSingleCourse = ($formation->courses->count() === 1);
                                                        @endphp
                                                        @foreach ($formation->courses as $course)
                                                            <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                                                                <div>
                                                                    <a href="{{ route('course-viewer', [$course]) }}"
                                                                        class="fw-bold text-decoration-none text-dark fs-6">
                                                                        <i class="bi bi-play-circle-fill text-primary me-2"></i>
                                                                        {{ $course->title }}
                                                                    </a>
                                                                    <div class="small text-muted mt-1">
                                                                        <i class="bi bi-layers me-1"></i> {{ $course->chapters->count() }} chapitre(s)
                                                                    </div>
                                                                </div>

                                                                <div class="d-flex align-items-center">
                                                                    <a href="{{ route('course-viewer', [$course]) }}" class="btn btn-sm btn-primary me-2 rounded-pill px-3">
                                                                        <i class="bi bi-play-fill"></i> Suivre le cours
                                                                    </a>
                                                                    <button class="btn btn-sm btn-outline-secondary rounded-circle"
                                                                        data-bs-toggle="collapse"
                                                                        data-bs-target="#course{{ $course->id }}"
                                                                        aria-expanded="{{ $isSingleCourse ? 'true' : 'false' }}">
                                                                        <i class="bi bi-chevron-{{ $isSingleCourse ? 'up' : 'down' }}"></i>
                                                                    </button>
                                                                </div>
                                                            </div>

                                                            <div id="course{{ $course->id }}" class="collapse bg-white {{ $isSingleCourse ? 'show' : '' }}">
                                                                <div class="list-group list-group-flush border-top">
                                                                    @foreach ($course->chapters as $chapter)
                                                                        <div class="list-group-item d-flex justify-content-between align-items-center ps-4 py-2">
                                                                            <span class="small">
                                                                                <i class="bi bi-file-earmark-text me-2 text-secondary"></i>
                                                                                {{ $chapter->title }}
                                                                            </span>
                                                                            <a href="{{ route('course-viewer', ['course' => $course->id, 'chapterId' => $chapter->id]) }}"
                                                                                class="btn btn-xs btn-outline-secondary rounded-pill px-2 text-decoration-none">
                                                                                <i class="bi bi-eye me-1"></i> Accéder
                                                                            </a>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Section 2 : Souscriptions En Attente de Validation -->
                    @if(isset($pendingSubscriptions) && !$pendingSubscriptions->isEmpty())
                        <div class="mt-4">
                            <h5 class="fw-bold text-warning mb-3">
                                <i class="bi bi-hourglass-split me-2"></i> Souscriptions En Attente de Validation
                            </h5>
                            <div class="card border-0 shadow-sm rounded-3 overflow-hidden border-start border-4 border-warning">
                                <div class="list-group list-group-flush">
                                    @foreach($pendingSubscriptions as $pendSub)
                                        @php
                                            $itemTitle = $pendSub->formation ? $pendSub->formation->title : ($pendSub->course ? $pendSub->course->title : ($pendSub->chapter ? $pendSub->chapter->title : 'Contenu Pédagogique'));
                                        @endphp
                                        <div class="list-group-item p-3 border-bottom">
                                            <!-- Ligne 1: Titre -->
                                            <div class="fw-bold text-dark fs-6 mb-1">
                                                <i class="bi bi-hourglass-top text-warning me-2"></i>{{ $itemTitle }}
                                            </div>

                                            <!-- Ligne 2: Type -->
                                            <div class="small text-muted mb-2">
                                                <i class="bi bi-tag me-1"></i> Type: {{ strtoupper($pendSub->type) }}
                                            </div>

                                            <!-- Ligne 3: Statut -->
                                            <div class="mb-2">
                                                <span class="badge bg-warning text-dark px-2.5 py-1.5 fw-semibold">
                                                    <i class="bi bi-clock-history me-1"></i> En attente de validation par l'administration
                                                </span>
                                            </div>

                                            <!-- Ligne 4: Informations -->
                                            <div class="small text-muted">
                                                <i class="bi bi-calendar-event me-1"></i> Demande soumise le {{ $pendSub->created_at ? $pendSub->created_at->format('d/m/Y à H:i') : 'N/A' }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Section 3 : Souscriptions Expirées -->
                    @if(isset($expiredSubscriptions) && !$expiredSubscriptions->isEmpty())
                        <div class="mt-4">
                            <h5 class="fw-bold text-danger mb-3">
                                <i class="bi bi-clock-history me-2"></i> Souscriptions Expirées
                            </h5>
                            <div class="card border-0 shadow-sm rounded-3 overflow-hidden border-start border-4 border-danger">
                                <div class="list-group list-group-flush">
                                    @foreach($expiredSubscriptions as $expSub)
                                        @php
                                            $itemTitle = $expSub->formation ? $expSub->formation->title : ($expSub->course ? $expSub->course->title : ($expSub->chapter ? $expSub->chapter->title : 'Contenu Pédagogique'));
                                        @endphp
                                        <div class="list-group-item p-3 border-bottom">
                                            <!-- Ligne 1: Titre -->
                                            <div class="fw-bold text-dark fs-6 mb-1">
                                                <i class="bi bi-journal-x text-danger me-2"></i>{{ $itemTitle }}
                                            </div>

                                            <!-- Ligne 2: Type -->
                                            <div class="small text-muted mb-2">
                                                <i class="bi bi-tag me-1"></i> Type: {{ strtoupper($expSub->type) }}
                                            </div>

                                            <!-- Ligne 3: Statut -->
                                            <div class="mb-2">
                                                <span class="badge bg-danger px-2.5 py-1.5 fw-semibold">
                                                    <i class="bi bi-exclamation-triangle-fill me-1"></i> Expirée le 
                                                    @if($expSub->expires_at)
                                                        {{ is_string($expSub->expires_at) ? \Carbon\Carbon::parse($expSub->expires_at)->format('d/m/Y') : $expSub->expires_at->format('d/m/Y') }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </span>
                                            </div>

                                            <!-- Ligne 4: Bouton d'action -->
                                            <div>
                                                <a href="{{ route('subscriptions.expired', $expSub->id) }}" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                                    <i class="bi bi-arrow-repeat me-1"></i> Demander prolongation
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            </div>

            <!-- Colonne Latérale : Journal des Activités de l'Étudiant -->
            <div class="col-md-4 p-3">
                @livewire('authenticated.student-activity-log')
            </div>
        </div>
    </div>

@endsection
