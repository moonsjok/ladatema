@extends('layouts.authenticated.owners.index')

@section('page-title', 'Tentatives d\'évaluations')

@push('scripts')
    <script>
        function sortAttempts(field) {
            const currentOrder = '{{ $sortOrder }}';
            const currentField = '{{ $sortBy }}';
            const newOrder = (field === currentField && currentOrder === 'asc') ? 'desc' : 'asc';

            const url = new URL(window.location);
            url.searchParams.set('sort_by', field);
            url.searchParams.set('sort_order', newOrder);

            window.location.href = url.toString();
        }

        function filterAttempts() {
            const student = document.getElementById('filter_student').value;
            const passed = document.getElementById('filter_passed').value;
            const evaluation = document.getElementById('filter_evaluation').value;

            const url = new URL(window.location);

            if (student) {
                url.searchParams.set('filter_student', student);
            } else {
                url.searchParams.delete('filter_student');
            }

            if (passed !== '') {
                url.searchParams.set('filter_passed', passed);
            } else {
                url.searchParams.delete('filter_passed');
            }

            if (evaluation) {
                url.searchParams.set('filter_evaluation', evaluation);
            } else {
                url.searchParams.delete('filter_evaluation');
            }

            url.searchParams.delete('page');

            window.location.href = url.toString();
        }

        document.addEventListener('DOMContentLoaded', function() {
            const studentInput = document.getElementById('filter_student');
            const evalInput = document.getElementById('filter_evaluation');

            if (studentInput) {
                studentInput.addEventListener('input', function() {
                    clearTimeout(this.filterTimeout);
                    this.filterTimeout = setTimeout(filterAttempts, 500);
                });
            }

            if (evalInput) {
                evalInput.addEventListener('input', function() {
                    clearTimeout(this.filterTimeout);
                    this.filterTimeout = setTimeout(filterAttempts, 500);
                });
            }

            const passedSelect = document.getElementById('filter_passed');
            if (passedSelect) {
                passedSelect.addEventListener('change', filterAttempts);
            }
        });

        function confirmDeleteAttempt(id) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Supprimer cette tentative ?',
                    text: 'Cette action supprimera également les réponses enregistrées.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Oui, supprimer !',
                    cancelButtonText: 'Annuler'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('delete-attempt-form-' + id).submit();
                    }
                });
            } else {
                if (confirm('Êtes-vous sûr de vouloir supprimer cette tentative ?')) {
                    document.getElementById('delete-attempt-form-' + id).submit();
                }
            }
        }
    </script>
@endpush

@section('dashboard-content')
<div class="container-fluid py-3">
    <!-- En-tête de la page -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="h3 mb-1 text-dark fw-bold">
                <i class="bi bi-journal-check text-primary me-2"></i> Historique & Analyse des Tentatives
            </h2>
            <p class="text-muted small mb-0">Consultez, filtrez et analysez les résultats de toutes les évaluations passées par les étudiants.</p>
        </div>
        <span class="badge bg-primary rounded-pill px-3 py-2 fs-7">
            <i class="bi bi-layers-fill me-1"></i> {{ $stats['total'] }} tentatives au total
        </span>
    </div>

    <!-- Cartes de statistiques style Google Dashboard -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 border-start border-4 border-primary bg-white h-100 p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase d-block mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Total Tentatives</span>
                        <h3 class="fw-bold mb-0 text-dark">{{ $stats['total'] }}</h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: #e8f0fe; color: #1a73e8;">
                        <i class="bi bi-journal-text fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 border-start border-4 border-success bg-white h-100 p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase d-block mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Réussies</span>
                        <h3 class="fw-bold mb-0 text-success">{{ $stats['passed'] }}</h3>
                        <small class="text-muted" style="font-size: 0.75rem;">
                            {{ $stats['total'] > 0 ? round(($stats['passed'] / $stats['total']) * 100, 1) : 0 }}% du total
                        </small>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: #dcfce7; color: #16a34a;">
                        <i class="bi bi-check-circle-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 border-start border-4 border-danger bg-white h-100 p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase d-block mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Échouées</span>
                        <h3 class="fw-bold mb-0 text-danger">{{ $stats['failed'] }}</h3>
                        <small class="text-muted" style="font-size: 0.75rem;">
                            {{ $stats['total'] > 0 ? round(($stats['failed'] / $stats['total']) * 100, 1) : 0 }}% du total
                        </small>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: #fee2e2; color: #dc2626;">
                        <i class="bi bi-x-circle-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 border-start border-4 border-info bg-white h-100 p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase d-block mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Moyenne globale</span>
                        <h3 class="fw-bold mb-0 text-info">{{ round($stats['average_score'], 1) }}%</h3>
                        <small class="text-muted" style="font-size: 0.75rem;">Score moyen des étudiants</small>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: #e0f2fe; color: #0284c7;">
                        <i class="bi bi-graph-up-arrow fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Panneau de Filtres et Recherche -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-light py-3 border-bottom">
            <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                <i class="bi bi-funnel-fill text-primary me-2"></i> Filtres de recherche avancés
            </h6>
        </div>
        <div class="card-body p-3.5">
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <label for="filter_student" class="form-label small fw-bold text-secondary">
                        <i class="bi bi-person-search me-1"></i> Étudiant (Nom ou Email)
                    </label>
                    <input type="text" class="form-control rounded-pill shadow-xs" id="filter_student" placeholder="Rechercher un étudiant..." value="{{ $filterStudent }}">
                </div>
                <div class="col-12 col-md-3">
                    <label for="filter_passed" class="form-label small fw-bold text-secondary">
                        <i class="bi bi-filter-circle me-1"></i> Résultat
                    </label>
                    <select class="form-select rounded-pill shadow-xs" id="filter_passed">
                        <option value="">Tous les résultats</option>
                        <option value="1" {{ $filterPassed === '1' ? 'selected' : '' }}>Réussis uniquement</option>
                        <option value="0" {{ $filterPassed === '0' ? 'selected' : '' }}>Échoués uniquement</option>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label for="filter_evaluation" class="form-label small fw-bold text-secondary">
                        <i class="bi bi-book-half me-1"></i> Évaluation
                    </label>
                    <input type="text" class="form-control rounded-pill shadow-xs" id="filter_evaluation" placeholder="Titre de l'évaluation..." value="{{ $filterEvaluation }}">
                </div>
                <div class="col-12 col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-secondary rounded-pill w-100 fw-semibold" onclick="window.location.href='{{ route('attempts.index') }}'">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Réinitialiser
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des Tentatives -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 fw-bold fs-6">
                <i class="bi bi-list-task me-2"></i> Liste des tentatives enregistrées ({{ $attempts->total() }})
            </h5>
        </div>
        <div class="card-body p-0">
            @if ($attempts->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light border-bottom">
                            <tr>
                                <th class="ps-3 py-3">
                                    <a href="javascript:void(0)" onclick="sortAttempts('student_name')" class="text-decoration-none text-dark fw-bold small">
                                        Étudiant
                                        @if ($sortBy === 'student_name')
                                            <i class="bi bi-sort-{{ $sortOrder === 'asc' ? 'alpha-down' : 'alpha-up-alt' }} text-primary ms-1"></i>
                                        @else
                                            <i class="bi bi-arrow-down-up opacity-50 ms-1" style="font-size: 0.75rem;"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="py-3">
                                    <a href="javascript:void(0)" onclick="sortAttempts('evaluation_title')" class="text-decoration-none text-dark fw-bold small">
                                        Évaluation
                                        @if ($sortBy === 'evaluation_title')
                                            <i class="bi bi-sort-{{ $sortOrder === 'asc' ? 'alpha-down' : 'alpha-up-alt' }} text-primary ms-1"></i>
                                        @else
                                            <i class="bi bi-arrow-down-up opacity-50 ms-1" style="font-size: 0.75rem;"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="py-3 text-center">
                                    <a href="javascript:void(0)" onclick="sortAttempts('score')" class="text-decoration-none text-dark fw-bold small">
                                        Score & Note
                                        @if ($sortBy === 'score')
                                            <i class="bi bi-sort-{{ $sortOrder === 'asc' ? 'numeric-down' : 'numeric-up-alt' }} text-primary ms-1"></i>
                                        @else
                                            <i class="bi bi-arrow-down-up opacity-50 ms-1" style="font-size: 0.75rem;"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="py-3 text-center">
                                    <a href="javascript:void(0)" onclick="sortAttempts('passed')" class="text-decoration-none text-dark fw-bold small">
                                        Résultat
                                        @if ($sortBy === 'passed')
                                            <i class="bi bi-sort-down text-primary ms-1"></i>
                                        @else
                                            <i class="bi bi-arrow-down-up opacity-50 ms-1" style="font-size: 0.75rem;"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="py-3">
                                    <a href="javascript:void(0)" onclick="sortAttempts('started_at')" class="text-decoration-none text-dark fw-bold small">
                                        Date & Temps
                                        @if ($sortBy === 'started_at')
                                            <i class="bi bi-sort-numeric-down text-primary ms-1"></i>
                                        @else
                                            <i class="bi bi-arrow-down-up opacity-50 ms-1" style="font-size: 0.75rem;"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="pe-3 py-3 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($attempts as $attempt)
                                <tr>
                                    <!-- Ligne Étudiant -->
                                    <td class="ps-3 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center me-2.5 flex-shrink-0" style="width: 38px; height: 38px; font-size: 0.9rem;">
                                                {{ strtoupper(substr($attempt->user->name ?? 'N', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark" style="font-size: 0.92rem;">
                                                    {{ $attempt->user->name ?? 'N/A' }}
                                                </div>
                                                <small class="text-muted d-block opacity-75" style="font-size: 0.78rem;">
                                                    {{ $attempt->user->email ?? 'N/A' }}
                                                </small>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Ligne Évaluation -->
                                    <td class="py-3">
                                        <div class="fw-semibold text-dark" style="font-size: 0.92rem;">
                                            {{ $attempt->evaluation->title ?? 'N/A' }}
                                        </div>
                                        @if(isset($attempt->evaluation->type))
                                            <span class="badge bg-light text-secondary border rounded-pill mt-0.5" style="font-size: 0.7rem;">
                                                {{ ucfirst($attempt->evaluation->type) }}
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Score & Note -->
                                    <td class="py-3 text-center">
                                        <div>
                                            <span class="badge bg-{{ $attempt->pourcentage >= 60 ? 'success' : 'danger' }}-subtle text-{{ $attempt->pourcentage >= 60 ? 'success' : 'danger' }} border border-{{ $attempt->pourcentage >= 60 ? 'success' : 'danger' }} rounded-pill px-2.5 py-1 fw-bold fs-7">
                                                {{ $attempt->pourcentage }}%
                                            </span>
                                            @if($attempt->grade)
                                                <span class="badge bg-secondary rounded-pill ms-1" style="font-size: 0.7rem;">{{ $attempt->grade }}</span>
                                            @endif
                                        </div>
                                        <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">
                                            {{ $attempt->score }} / {{ $attempt->total_points }} pts
                                        </small>
                                    </td>

                                    <!-- Résultat -->
                                    <td class="py-3 text-center">
                                        @if ($attempt->passed)
                                            <span class="badge bg-success rounded-pill px-3 py-1.5 fw-semibold">
                                                <i class="bi bi-check-circle-fill me-1"></i> Réussi
                                            </span>
                                        @else
                                            <span class="badge bg-danger rounded-pill px-3 py-1.5 fw-semibold">
                                                <i class="bi bi-x-circle-fill me-1"></i> Échoué
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Date & Temps -->
                                    <td class="py-3">
                                        <div class="small text-dark fw-semibold" style="font-size: 0.82rem;">
                                            <i class="bi bi-calendar-event me-1 text-primary"></i>
                                            {{ $attempt->created_at ? $attempt->created_at->format('d/m/Y à H:i') : 'N/A' }}
                                        </div>
                                        @if ($attempt->time_spent)
                                            <small class="text-muted d-block mt-0.5" style="font-size: 0.76rem;">
                                                <i class="bi bi-stopwatch me-1 text-secondary"></i>
                                                {{ gmdate('H:i:s', $attempt->time_spent) }}
                                            </small>
                                        @endif
                                    </td>

                                    <!-- Actions -->
                                    <td class="pe-3 py-3 text-end">
                                        <div class="d-inline-flex gap-1">
                                            <a href="{{ route('attempts.show', $attempt->id) }}" 
                                               class="btn btn-sm btn-outline-primary rounded-circle" 
                                               title="Voir le détail complet">
                                                <i class="bi bi-eye-fill"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger rounded-circle" 
                                                    title="Supprimer la tentative"
                                                    onclick="confirmDeleteAttempt({{ $attempt->id }})">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                            <form id="delete-attempt-form-{{ $attempt->id }}" 
                                                  action="{{ route('attempts.destroy', $attempt->id) }}" 
                                                  method="POST" 
                                                  class="d-none">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="p-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="text-muted small">
                        Affichage de <strong>{{ $attempts->firstItem() }}</strong> à <strong>{{ $attempts->lastItem() }}</strong> sur <strong>{{ $attempts->total() }}</strong> tentatives
                    </div>
                    <div>
                        {{ $attempts->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-search fs-1 text-muted mb-2 d-block"></i>
                    <h5 class="text-dark fw-bold mb-1">Aucune tentative trouvée</h5>
                    <p class="text-muted small mb-3">
                        @if ($filterStudent || $filterPassed !== null || $filterEvaluation)
                            Aucun résultat ne correspond à vos filtres actuels.
                        @else
                            Aucune tentative d'évaluation n'a encore été enregistrée.
                        @endif
                    </p>
                    @if ($filterStudent || $filterPassed !== null || $filterEvaluation)
                        <a href="{{ route('attempts.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-4 fw-semibold">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Réinitialiser les filtres
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
