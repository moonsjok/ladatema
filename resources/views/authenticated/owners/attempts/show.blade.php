@extends('layouts.authenticated.owners.index')
@section('page-title', 'Détails de la tentative #' . $attempt->id)
@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .question-card {
            border-left: 4px solid #007bff;
            margin-bottom: 1rem;
        }
        .question-card.correct {
            border-left-color: #28a745;
        }
        .question-card.incorrect {
            border-left-color: #dc3545;
        }
        .answer-option {
            padding: 0.5rem;
            margin: 0.25rem 0;
            border-radius: 0.25rem;
        }
        .answer-option.selected {
            background-color: #e3f2fd;
            border: 1px solid #2196f3;
        }
        .answer-option.correct {
            background-color: #d4edda;
            border: 1px solid #28a745;
        }
        .answer-option.incorrect {
            background-color: #f8d7da;
            border: 1px solid #dc3545;
        }
        .stat-card {
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-2px);
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function printResults() {
            window.print();
        }
        
        function exportToPDF() {
            // Implémentation future pour l'export PDF
            alert('Export PDF en cours de développement...');
        }
    </script>
@endpush

@section('dashboard-content')
    <div class="container-fluid">
        <!-- En-tête avec actions -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="mb-1">Détails de la tentative #{{ $attempt->id }}</h3>
                <p class="text-muted mb-0">
                    <i class="fas fa-clock me-1"></i>
                    {{ $attempt->created_at->format('d/m/Y H:i') }}
                </p>
            </div>
            <div class="btn-group">
                <a href="{{ route('attempts.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>
                    Retour à la liste
                </a>
                <button onclick="printResults()" class="btn btn-outline-primary">
                    <i class="fas fa-print me-1"></i>
                    Imprimer
                </button>
                <button onclick="exportToPDF()" class="btn btn-outline-success">
                    <i class="fas fa-file-pdf me-1"></i>
                    Export PDF
                </button>
            </div>
        </div>

        <!-- Cartes de statistiques -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card stat-card h-100 {{ $attempt->passed ? 'border-success' : 'border-danger' }}">
                    <div class="card-body text-center">
                        <i class="fas fa-trophy fa-2x mb-2 {{ $attempt->passed ? 'text-success' : 'text-danger' }}"></i>
                        <h4 class="card-title">{{ $attempt->grade }}</h4>
                        <p class="card-text">Note obtenue</p>
                        <span class="badge {{ $attempt->passed ? 'bg-success' : 'bg-danger' }}">
                            {{ $attempt->passed ? 'Réussi' : 'Échoué' }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card h-100 border-primary">
                    <div class="card-body text-center">
                        <i class="fas fa-percentage fa-2x mb-2 text-primary"></i>
                        <h4 class="card-title">{{ $attempt->pourcentage }}%</h4>
                        <p class="card-text">Pourcentage</p>
                        <small class="text-muted">{{ $attempt->score }}/{{ $attempt->total_points }} pts</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card h-100 border-info">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                        <h4 class="card-title">{{ $stats['correct_answers'] }}/{{ $stats['total_questions'] }}</h4>
                        <p class="card-text">Réponses correctes</p>
                        <small class="text-muted">{{ round(($stats['correct_answers'] / $stats['total_questions']) * 100, 1) }}%</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card h-100 border-warning">
                    <div class="card-body text-center">
                        <i class="fas fa-clock fa-2x mb-2 text-warning"></i>
                        <h4 class="card-title">{{ gmdate('H:i:s', $attempt->time_spent ?? 0) }}</h4>
                        <p class="card-text">Temps total</p>
                        <small class="text-muted">{{ $stats['time_per_question'] }}s/question</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations sur l'étudiant et l'évaluation -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-user me-2"></i>
                            Étudiant
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar-lg bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                                {{ strtoupper(substr($attempt->user->name ?? 'N/A', 0, 1)) }}
                            </div>
                            <div>
                                <h6 class="mb-1">{{ $attempt->user->name ?? 'N/A' }}</h6>
                                <p class="text-muted mb-0">{{ $attempt->user->email ?? 'N/A' }}</p>
                                <small class="badge bg-secondary">{{ $attempt->user->role ?? 'N/A' }}</small>
                            </div>
                        </div>
                        @if($attempt->user->phone_call || $attempt->user->phone_whatsapp)
                            <div class="row text-sm">
                                @if($attempt->user->phone_call)
                                    <div class="col-6">
                                        <small class="text-muted">Appel:</small><br>
                                        {{ $attempt->user->phone_call }}
                                    </div>
                                @endif
                                @if($attempt->user->phone_whatsapp)
                                    <div class="col-6">
                                        <small class="text-muted">WhatsApp:</small><br>
                                        {{ $attempt->user->phone_whatsapp }}
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-clipboard-list me-2"></i>
                            Évaluation
                        </h5>
                    </div>
                    <div class="card-body">
                        <h6 class="mb-2">{{ $attempt->evaluation->title ?? 'N/A' }}</h6>
                        <p class="text-muted mb-2">{{ $attempt->evaluation->description ?? 'N/A' }}</p>
                        
                        <div class="row text-sm">
                            <div class="col-6">
                                <small class="text-muted">Type:</small><br>
                                <span class="badge bg-info">{{ $attempt->evaluation->evaluatable_type ?? 'N/A' }}</span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Importance:</small><br>
                                <span class="badge bg-{{ $attempt->evaluation->getImportanceColorAttribute() }}">
                                    {{ $attempt->evaluation->getImportanceLabelAttribute() }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="row text-sm mt-2">
                            <div class="col-6">
                                <small class="text-muted">Durée:</small><br>
                                {{ $attempt->evaluation->duration ?? 'N/A' }}
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Score minimal:</small><br>
                                {{ $attempt->evaluation->passing_score ?? 'N/A' }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline de la tentative -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2"></i>
                    Chronologie
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-center">
                            <i class="fas fa-play-circle fa-2x text-success mb-2"></i>
                            <h6>Début</h6>
                            <p class="text-muted">{{ $attempt->started_at ? $attempt->started_at->format('d/m/Y H:i:s') : 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <i class="fas fa-stop-circle fa-2x text-danger mb-2"></i>
                            <h6>Fin</h6>
                            <p class="text-muted">{{ $attempt->completed_at ? $attempt->completed_at->format('d/m/Y H:i:s') : 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <i class="fas fa-hourglass-half fa-2x text-warning mb-2"></i>
                            <h6>Durée</h6>
                            <p class="text-muted">{{ gmdate('H:i:s', $attempt->time_spent ?? 0) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Réponses par type de question -->
        @if($answersByType->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie me-2"></i>
                        Réponses par type de question
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($answersByType as $type => $answers)
                            <div class="col-md-3 mb-3">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">{{ $type }}</h6>
                                        <p class="card-text">
                                            <span class="badge bg-success">{{ $answers->filter(fn($a) => $a->isCorrect())->count() }}</span> /
                                            <span class="badge bg-secondary">{{ $answers->count() }}</span>
                                        </p>
                                        <small class="text-muted">
                                            {{ round(($answers->filter(fn($a) => $a->isCorrect())->count() / $answers->count()) * 100, 1) }}% de réussite
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Détail des questions et réponses -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-list-alt me-2"></i>
                    Détail des questions et réponses ({{ $attempt->studentAnswers->count() }} questions)
                </h5>
            </div>
            <div class="card-body">
                @foreach($attempt->studentAnswers as $index => $studentAnswer)
                    <div class="card question-card {{ $studentAnswer->isCorrect() ? 'correct' : 'incorrect' }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="flex-grow-1">
                                    <h6 class="card-title mb-2">
                                        Question {{ $index + 1 }}: 
                                        <span class="badge bg-info">{{ $studentAnswer->question->getTypeLabel() }}</span>
                                        <span class="badge bg-secondary">{{ $studentAnswer->question->points }} pts</span>
                                    </h6>
                                    <p class="card-text">{{ $studentAnswer->question->question_text }}</p>
                                </div>
                                <div class="text-end">
                                    @if($studentAnswer->isCorrect())
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>
                                            Correct ({{ $studentAnswer->question->points }} pts)
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times me-1"></i>
                                            Incorrect (0 pts)
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Options de réponse -->
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-2">Réponse de l'étudiant:</h6>
                                    @if($studentAnswer->question->type === \App\Models\Question::TYPE_TEXT)
                                        <div class="answer-option selected">
                                            <i class="fas fa-user-edit me-2"></i>
                                            {{ $studentAnswer->answer->answer_text ?? 'Non répondue' }}
                                        </div>
                                    @else
                                        @foreach($studentAnswer->question->answers as $answer)
                                            <div class="answer-option {{ $answer->id === $studentAnswer->answer_id ? 'selected' : '' }} {{ $answer->is_correct ? 'correct' : '' }}">
                                                <i class="fas fa-{{ $answer->id === $studentAnswer->answer_id ? 'check-square' : 'square' }} me-2"></i>
                                                {{ $answer->answer_text }}
                                                @if($answer->id === $studentAnswer->answer_id)
                                                    <span class="badge bg-primary ms-2">Votre choix</span>
                                                @endif
                                                @if($answer->is_correct)
                                                    <span class="badge bg-success ms-2">Correct</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                                <!-- Réponse correcte et explication -->
                                <div class="col-md-6">
                                    @if(!$studentAnswer->isCorrect())
                                        <h6 class="text-muted mb-2">Réponse correcte:</h6>
                                        @if($studentAnswer->question->type !== \App\Models\Question::TYPE_TEXT)
                                            @foreach($studentAnswer->question->answers->where('is_correct', true) as $correctAnswer)
                                                <div class="answer-option correct">
                                                    <i class="fas fa-check-circle me-2"></i>
                                                    {{ $correctAnswer->answer_text }}
                                                </div>
                                            @endforeach
                                        @endif
                                    @endif

                                    @if($studentAnswer->answer->explanation)
                                        <div class="mt-3">
                                            <h6 class="text-muted mb-2">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Explication:
                                            </h6>
                                            <div class="alert alert-info">
                                                {{ $studentAnswer->answer->explanation }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
