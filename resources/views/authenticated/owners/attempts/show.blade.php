@extends('layouts.authenticated.owners.index')

@section('page-title', 'Détails de la tentative #' . $attempt->id)

@push('styles')
    <style>
        .question-card {
            border-left: 4px solid #0d6efd;
            margin-bottom: 1.25rem;
            transition: all 0.2s ease-in-out;
        }
        .question-card.correct {
            border-left-color: #198754;
        }
        .question-card.incorrect {
            border-left-color: #dc3545;
        }
        .answer-option {
            padding: 0.75rem 1rem;
            margin: 0.35rem 0;
            border-radius: 0.5rem;
            border: 1px solid #e2e8f0;
            transition: all 0.15s ease-in-out;
        }
        .answer-option.selected {
            background-color: #eff6ff;
            border-color: #3b82f6;
        }
        .answer-option.correct {
            background-color: #dcfce7;
            border-color: #22c55e;
            color: #15803d;
        }
        .answer-option.incorrect {
            background-color: #fee2e2;
            border-color: #ef4444;
            color: #b91c1c;
        }
        .stat-card {
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-3px);
        }
    </style>
@endpush

@push('scripts')
    <script>
        function printResults() {
            window.print();
        }
    </script>
@endpush

@section('dashboard-content')
<div class="container-fluid py-3">
    <!-- En-tête avec actions -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="h3 mb-1 text-dark fw-bold">
                <i class="bi bi-journal-check text-primary me-2"></i> Tentative #{{ $attempt->id }}
            </h2>
            <p class="text-muted small mb-0">
                <i class="bi bi-clock me-1 text-primary"></i>
                Soumise le {{ $attempt->created_at ? $attempt->created_at->format('d/m/Y à H:i') : 'N/A' }}
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('attempts.index') }}" class="btn btn-outline-secondary rounded-pill px-3 fw-semibold">
                <i class="bi bi-arrow-left me-1"></i> Retour à la liste
            </a>
            <button onclick="printResults()" class="btn btn-outline-primary rounded-pill px-3 fw-semibold">
                <i class="bi bi-printer me-1"></i> Imprimer
            </button>
        </div>
    </div>

    <!-- Cartes de statistiques style Google Dashboard -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card border-0 shadow-sm rounded-3 border-start border-4 border-{{ $attempt->passed ? 'success' : 'danger' }} bg-white h-100 p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase d-block mb-1" style="font-size: 0.75rem;">Note & Résultat</span>
                        <h3 class="fw-bold mb-0 text-{{ $attempt->passed ? 'success' : 'danger' }}">{{ $attempt->grade ?? 'N/A' }}</h3>
                        <span class="badge bg-{{ $attempt->passed ? 'success' : 'danger' }} rounded-pill px-2.5 py-1 mt-1">
                            <i class="bi bi-{{ $attempt->passed ? 'check-circle' : 'x-circle' }} me-1"></i>
                            {{ $attempt->passed ? 'Réussi' : 'Échoué' }}
                        </span>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: {{ $attempt->passed ? '#dcfce7' : '#fee2e2' }}; color: {{ $attempt->passed ? '#16a34a' : '#dc2626' }};">
                        <i class="bi bi-trophy-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card border-0 shadow-sm rounded-3 border-start border-4 border-primary bg-white h-100 p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase d-block mb-1" style="font-size: 0.75rem;">Pourcentage</span>
                        <h3 class="fw-bold mb-0 text-primary">{{ $attempt->pourcentage }}%</h3>
                        <small class="text-muted" style="font-size: 0.78rem;">{{ $attempt->score }} / {{ $attempt->total_points }} pts</small>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: #e8f0fe; color: #1a73e8;">
                        <i class="bi bi-percent fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card border-0 shadow-sm rounded-3 border-start border-4 border-info bg-white h-100 p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase d-block mb-1" style="font-size: 0.75rem;">Réponses Correctes</span>
                        <h3 class="fw-bold mb-0 text-info">{{ $stats['correct_answers'] }} / {{ $stats['total_questions'] }}</h3>
                        <small class="text-muted" style="font-size: 0.78rem;">
                            {{ $stats['total_questions'] > 0 ? round(($stats['correct_answers'] / $stats['total_questions']) * 100, 1) : 0 }}% d'exactitude
                        </small>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: #e0f2fe; color: #0284c7;">
                        <i class="bi bi-check-all fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card border-0 shadow-sm rounded-3 border-start border-4 border-warning bg-white h-100 p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase d-block mb-1" style="font-size: 0.75rem;">Temps Passé</span>
                        <h3 class="fw-bold mb-0 text-dark" style="font-size: 1.25rem;">{{ gmdate('H:i:s', $attempt->time_spent ?? 0) }}</h3>
                        <small class="text-muted" style="font-size: 0.78rem;">Moy. {{ $stats['time_per_question'] }}s / question</small>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: #fef3c7; color: #d97706;">
                        <i class="bi bi-stopwatch-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Infos Étudiant et Évaluation -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-light py-3 border-bottom">
                    <h6 class="card-title mb-0 fw-bold text-dark">
                        <i class="bi bi-person-badge-fill text-primary me-2"></i> Informations Étudiant
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 50px; height: 50px; font-size: 1.2rem;">
                            {{ strtoupper(substr($attempt->user->name ?? 'N', 0, 1)) }}
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-1" style="font-size: 1.05rem;">{{ $attempt->user->name ?? 'N/A' }}</h5>
                            <p class="text-muted small mb-1">{{ $attempt->user->email ?? 'N/A' }}</p>
                            <span class="badge bg-secondary rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">Rôle : {{ ucfirst($attempt->user->role ?? 'student') }}</span>
                        </div>
                    </div>
                    @if($attempt->user->phone_call || $attempt->user->phone_whatsapp)
                        <div class="row pt-2 border-top g-2 small">
                            @if($attempt->user->phone_call)
                                <div class="col-6">
                                    <span class="text-muted d-block"><i class="bi bi-telephone me-1 text-primary"></i> Appel :</span>
                                    <strong>{{ $attempt->user->phone_call }}</strong>
                                </div>
                            @endif
                            @if($attempt->user->phone_whatsapp)
                                <div class="col-6">
                                    <span class="text-muted d-block"><i class="bi bi-whatsapp me-1 text-success"></i> WhatsApp :</span>
                                    <strong>{{ $attempt->user->phone_whatsapp }}</strong>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-light py-3 border-bottom">
                    <h6 class="card-title mb-0 fw-bold text-dark">
                        <i class="bi bi-card-checklist text-primary me-2"></i> Détails de l'Évaluation
                    </h6>
                </div>
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-2" style="font-size: 1.05rem;">{{ $attempt->evaluation->title ?? 'N/A' }}</h5>
                    <p class="text-secondary small mb-3">{{ $attempt->evaluation->description ?? 'Aucune description.' }}</p>
                    
                    <div class="row pt-2 border-top g-2 small">
                        <div class="col-6">
                            <span class="text-muted d-block">Support / Module :</span>
                            <span class="badge bg-primary-subtle text-primary border rounded-pill px-2.5 py-1">
                                {{ $attempt->evaluation->evaluatable->title ?? 'Évaluation' }}
                            </span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block">Questions :</span>
                            <strong>{{ $stats['total_questions'] }} questions</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Réponses de l'étudiant -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-dark text-white py-3">
            <h5 class="card-title mb-0 fw-bold fs-6">
                <i class="bi bi-list-check me-2"></i> Réponses détaillées par question
            </h5>
        </div>
        <div class="card-body p-4">
            @if($attempt->studentAnswers && $attempt->studentAnswers->count() > 0)
                @foreach($attempt->studentAnswers as $index => $answer)
                    <div class="card question-card {{ $answer->isCorrect() ? 'correct' : 'incorrect' }} shadow-xs rounded-3 p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="fw-bold text-dark mb-0">
                                Question #{{ $index + 1 }} : {{ $answer->question->question_text ?? 'Intitulé de la question' }}
                            </h6>
                            <span class="badge bg-{{ $answer->isCorrect() ? 'success' : 'danger' }} rounded-pill px-2.5 py-1">
                                <i class="bi bi-{{ $answer->isCorrect() ? 'check-lg' : 'x-lg' }} me-1"></i>
                                {{ $answer->isCorrect() ? 'Correct' : 'Incorrect' }}
                            </span>
                        </div>

                        <div class="small text-muted mb-2">
                            Type : <strong>{{ $answer->question->getTypeLabel() }}</strong> • Points : <strong>{{ $answer->points_earned ?? ($answer->isCorrect() ? 1 : 0) }} / {{ $answer->question->points ?? 1 }}</strong>
                        </div>

                        <!-- Réponse de l'étudiant -->
                        <div class="answer-option {{ $answer->isCorrect() ? 'correct' : 'incorrect' }} mb-2">
                            <span class="fw-bold d-block small">Réponse de l'étudiant :</span>
                            <span>{{ $answer->answer ? $answer->answer->answer_text : ($answer->user_answer_text ?? 'Aucune réponse') }}</span>
                        </div>

                        @if($answer->question->explanation)
                            <div class="p-2.5 bg-light rounded-3 small border-start border-3 border-info mt-2">
                                <strong class="text-info"><i class="bi bi-info-circle me-1"></i> Explication :</strong>
                                <span class="text-secondary">{!! $answer->question->explanation !!}</span>
                            </div>
                        @endif
                    </div>
                @endforeach
            @else
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-inbox fs-2 d-block mb-1"></i>
                    <p class="mb-0 small">Aucune réponse détaillée enregistrée pour cette tentative.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
