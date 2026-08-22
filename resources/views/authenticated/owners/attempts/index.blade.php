@extends('layouts.authenticated.owners.index')
@section('page-title', 'Les tentatives')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/evaluation-creation.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fonction pour trier
        function sortAttempts(field) {
            const currentOrder = '{{ $sortOrder }}';
            const currentField = '{{ $sortBy }}';
            const newOrder = (field === currentField && currentOrder === 'asc') ? 'desc' : 'asc';

            const url = new URL(window.location);
            url.searchParams.set('sort_by', field);
            url.searchParams.set('sort_order', newOrder);

            window.location.href = url.toString();
        }

        // Fonction pour filtrer
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

            url.searchParams.delete('page'); // Réinitialiser la page

            window.location.href = url.toString();
        }

        // Gestionnaire d'événements pour le filtre
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('filter_student').addEventListener('input', function() {
                clearTimeout(this.filterTimeout);
                this.filterTimeout = setTimeout(filterAttempts, 500);
            });

            document.getElementById('filter_passed').addEventListener('change', filterAttempts);
            document.getElementById('filter_evaluation').addEventListener('input', function() {
                clearTimeout(this.filterTimeout);
                this.filterTimeout = setTimeout(filterAttempts, 500);
            });
        });
    </script>
@endpush

@section('dashboard-content')
    <div class="container-fluid">
        <!-- Cartes de statistiques -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title">{{ $stats['total'] }}</h4>
                                <p class="card-text">Total tentatives</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-chart-line fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title">{{ $stats['passed'] }}</h4>
                                <p class="card-text">Réussies</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title">{{ $stats['failed'] }}</h4>
                                <p class="card-text">Échouées</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-times-circle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title">{{ round($stats['average_score'], 1) }}%</h4>
                                <p class="card-text">Moyenne</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-percentage fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-filter me-2"></i>
                    Filtres et tri
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <label for="filter_student" class="form-label">Étudiant</label>
                        <input type="text" class="form-control" id="filter_student" placeholder="Nom ou email..."
                            value="{{ $filterStudent }}">
                    </div>
                    <div class="col-md-3">
                        <label for="filter_passed" class="form-label">Résultat</label>
                        <select class="form-select" id="filter_passed">
                            <option value="">Tous</option>
                            <option value="1" {{ $filterPassed === '1' ? 'selected' : '' }}>Réussis</option>
                            <option value="0" {{ $filterPassed === '0' ? 'selected' : '' }}>Échoués</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filter_evaluation" class="form-label">Évaluation</label>
                        <input type="text" class="form-control" id="filter_evaluation"
                            placeholder="Titre de l'évaluation..." value="{{ $filterEvaluation }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="button" class="btn btn-outline-secondary me-2"
                            onclick="window.location.href='{{ route('attempts.index') }}'">
                            <i class="fas fa-redo me-1"></i>
                            Réinitialiser
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tableau des tentatives -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    Liste des tentatives ({{ $attempts->total() }} résultats)
                </h5>
            </div>
            <div class="card-body">
                @if ($attempts->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        <a href="javascript:void(0)" onclick="sortAttempts('student_name')"
                                            class="text-decoration-none">
                                            Étudiant
                                            @if ($sortBy === 'student_name')
                                                <i class="fas fa-sort-{{ $sortOrder === 'asc' ? 'up' : 'down' }}"></i>
                                            @else
                                                <i class="fas fa-sort"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>
                                        <a href="javascript:void(0)" onclick="sortAttempts('evaluation_title')"
                                            class="text-decoration-none">
                                            Évaluation
                                            @if ($sortBy === 'evaluation_title')
                                                <i class="fas fa-sort-{{ $sortOrder === 'asc' ? 'up' : 'down' }}"></i>
                                            @else
                                                <i class="fas fa-sort"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>
                                        <a href="javascript:void(0)" onclick="sortAttempts('score')"
                                            class="text-decoration-none">
                                            Score
                                            @if ($sortBy === 'score')
                                                <i class="fas fa-sort-{{ $sortOrder === 'asc' ? 'up' : 'down' }}"></i>
                                            @else
                                                <i class="fas fa-sort"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>
                                        <a href="javascript:void(0)" onclick="sortAttempts('passed')"
                                            class="text-decoration-none">
                                            Résultat
                                            @if ($sortBy === 'passed')
                                                <i class="fas fa-sort-{{ $sortOrder === 'asc' ? 'up' : 'down' }}"></i>
                                            @else
                                                <i class="fas fa-sort"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>
                                        <a href="javascript:void(0)" onclick="sortAttempts('started_at')"
                                            class="text-decoration-none">
                                            Date
                                            @if ($sortBy === 'started_at')
                                                <i class="fas fa-sort-{{ $sortOrder === 'asc' ? 'up' : 'down' }}"></i>
                                            @else
                                                <i class="fas fa-sort"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>Temps</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($attempts as $attempt)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div
                                                    class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    {{ strtoupper(substr($attempt->user->name ?? 'N/A', 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ $attempt->user->name ?? 'N/A' }}</div>
                                                    <small class="text-muted">{{ $attempt->user->email ?? 'N/A' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <div class="fw-semibold">{{ $attempt->evaluation->title ?? 'N/A' }}</div>
                                                <small
                                                    class="text-muted">{{ $attempt->evaluation->type ?? 'N/A' }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span
                                                    class="fw-bold {{ $attempt->pourcentage >= 60 ? 'text-success' : 'text-danger' }}">
                                                    {{ $attempt->pourcentage }}%
                                                </span>
                                                <span class="badge bg-secondary ms-2">{{ $attempt->grade }}</span>
                                            </div>
                                            <small
                                                class="text-muted">{{ $attempt->score }}/{{ $attempt->total_points }}</small>
                                        </td>
                                        <td>
                                            @if ($attempt->passed)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check me-1"></i>Réussi
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times me-1"></i>Échoué
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div>
                                                <div>
                                                    {{ $attempt->started_at ? $attempt->started_at->format('d/m/Y H:i') : 'N/A' }}
                                                </div>
                                                @if ($attempt->completed_at)
                                                    <small class="text-muted">Fin:
                                                        {{ $attempt->completed_at->format('H:i') }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if ($attempt->time_spent)
                                                <span class="badge bg-info">
                                                    <i class="fas fa-clock me-1"></i>
                                                    {{ gmdate('H:i:s', $attempt->time_spent) }}
                                                </span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('attempts.show', $attempt->id) }}"
                                                    class="btn btn-outline-primary" title="Voir les détails">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="#" class="btn btn-outline-info" title="Voir les réponses">
                                                    <i class="fas fa-list-alt"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="row mt-4">
                        <div class="col-12 d-flex justify-content-between align-items-center">
                            <div class="text-muted">
                                Affichage de {{ $attempts->firstItem() }} à {{ $attempts->lastItem() }}
                                sur {{ $attempts->total() }} résultats
                            </div>
                            <div>
                                {{ $attempts->links() }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Aucune tentative trouvée</h5>
                        <p class="text-muted">
                            @if ($filterStudent || $filterPassed !== null || $filterEvaluation)
                                Essayez de modifier les filtres pour voir plus de résultats.
                            @else
                                Aucune tentative n'a été enregistrée pour le moment.
                            @endif
                        </p>
                        @if ($filterStudent || $filterPassed !== null || $filterEvaluation)
                            <a href="{{ route('attempts.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-redo me-1"></i>
                                Réinitialiser les filtres
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
