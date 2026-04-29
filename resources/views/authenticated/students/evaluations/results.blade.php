@extends('layouts.authenticated.students.index')
@section('page-title', "Résultats - " . $evaluation->title)

@section('dashboard-content')
    <div class="container my-6">
        <!-- Carte de résultat principal -->
        <div class="card mb-4">
            <div class="card-body text-center">
                @if ($attempt->passed)
                    <div class="icon-circle bg-success mx-auto mb-3">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <h4 class="text-success mb-2">Félicitations !</h4>
                    <p class="text-muted mb-4">Vous avez réussi cette évaluation</p>
                @else
                    <div class="icon-circle bg-danger mx-auto mb-3">
                        <i class="bi bi-x-circle-fill"></i>
                    </div>
                    <h4 class="text-danger mb-2">Évaluation non réussie</h4>
                    <p class="text-muted mb-4">Vous n'avez pas atteint le score requis</p>
                @endif
                
                <div class="row justify-content-center mb-4">
                    <div class="col-md-3">
                        <div class="h2 mb-1">{{ $attempt->score }}/{{ $attempt->total_points }}</div>
                        <small class="text-muted">Score obtenu</small>
                    </div>
                    <div class="col-md-3">
                        <div class="h2 mb-1">{{ $attempt->pourcentage }}%</div>
                        <small class="text-muted">Pourcentage</small>
                    </div>
                    <div class="col-md-3">
                        <div class="h2 mb-1">{{ $evaluation->passing_score ?? 60 }}   {!! $evaluation->scoring_mode == 'points' ? "<small>Points</small>" :" %" !!} </div>
                        <small class="text-muted">Score requis</small>
                    </div>
                    <div class="col-md-3">
                        <div class="h2 mb-1">
                        {{-- @php
                            // S'assurer que le temps est positif pour le formatage
                            $timeForFormatting = max(0, $attempt->time_spent);
                            
                            $minutes = floor($timeForFormatting / 60);
                            $seconds = $timeForFormatting % 60;
                            $formattedTimeSpent = sprintf("%02d:%02d", $minutes, $seconds);
                        @endphp
                        {{  $formattedTimeSpent }}  --}}
                        {{formateTime($attempt->time_spent)}}
                        
                        </div>
                        <small class="text-muted">Temps passé</small>
                    </div>
                </div>
                
                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ route('student.evaluations.show', $evaluation) }}" 
                       class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left me-2"></i>Retour à l'évaluation
                    </a>
                    @if (!$attempt->passed && (!$evaluation->max_attempts || $attempt->user->attempts()->where('evaluation_id', $evaluation->id)->count() < $evaluation->max_attempts))
                        <a href="{{ route('student.evaluations.start', $evaluation) }}" 
                           class="btn btn-primary">
                            <i class="bi bi-arrow-clockwise me-2"></i>Retenter
                        </a>
                    @endif
                </div>


            </div>
        </div>

        <!-- Détail des réponses -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-list-check me-2"></i>
                    Détail de vos réponses
                </h6>
            </div>
            <div class="card-body">
                @foreach ($evaluation->questions()->with('answers')->get() as $index => $question)
                    <div class="border rounded p-3 mb-3">
                        <div class="d-flex align-items-start mb-2">
                            <span class="badge bg-primary me-2">{{ $index + 1 }}</span>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $question->question_text }}</h6>
                                <small class="text-muted">{{ $question->points }} points</small>
                            </div>
                            @php
                                $studentAnswer = $attempt->studentAnswers()->where('question_id', $question->id)->first();
                                $isCorrect = $studentAnswer && $studentAnswer->answer->is_correct;
                            @endphp
                            <div class="ms-3">
                                @if ($isCorrect)
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>Correct
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        <i class="bi bi-x-circle me-1"></i>Incorrect
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <small class="text-muted d-block mb-2">Votre réponse:</small>
                                @if ($studentAnswer)
                                    <div class="p-2 border rounded @if ($isCorrect) border-success bg-light @else border-danger bg-light @endif">
                                        {{ $studentAnswer->answer->answer_text }}
                                    </div>
                                @else
                                    <div class="p-2 border rounded bg-secondary text-muted">
                                        Non répondue
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block mb-2">Réponse(s) correcte(s):</small>
                                @php
                                    $correctAnswers = $question->answers()->where('is_correct', true)->get();
                                @endphp
                                
                                @if($correctAnswers->count() > 0)
                                    @foreach($correctAnswers as $index => $correctAnswer)
                                        <div class="p-2 border rounded border-success bg-light mb-2">
                                            <strong>{{ chr(65 + $index) }})</strong> {{ $correctAnswer->answer_text }}
                                        </div>
                                        
                                        @if($correctAnswer->explanation)
                                            <div class="mb-3">
                                                <small class="text-muted d-block mb-1">Explication:</small>
                                                <div class="p-2 border rounded border-info bg-light-blue">
                                                    <i class="bi bi-info-circle text-info me-1"></i>
                                                    {{ $correctAnswer->explanation }}
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @else
                                    <div class="p-2 border rounded border-warning bg-light">
                                        <i class="bi bi-exclamation-triangle text-warning me-1"></i>
                                        Aucune réponse correcte définie
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <style>
        .icon-circle {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: var(--bs-primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .icon-circle.bg-success {
            background: var(--bs-success);
        }

        .bg-light-blue {
            background-color: #e7f5ff !important;
        }

        .icon-circle.bg-danger {
            background: var(--bs-danger);
        }
    </style>
@endsection
