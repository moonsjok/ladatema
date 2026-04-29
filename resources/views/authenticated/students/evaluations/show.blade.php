@extends('layouts.authenticated.students.index')
@section('page-title', "Évaluation - " . $evaluation->title)

@section('dashboard-content')
    <div class="container my-6">
        <!-- Carte d'information principale -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center mb-4">
                    <div class="col-md-8">
                        <h4 class="mb-2">
                            <i class="bi bi-clipboard text-primary me-2"></i>
                            {{ $evaluation->title }}
                        </h4>
                        <p class="text-muted mb-0">{{ $evaluation->description }}</p>
                        <span class="badge bg-{{ $evaluation->importance_color }}">
                                {{ $evaluation->importance_label }}
                        </span>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="badge bg-info fs-6">
                            <i class="bi bi-tag me-1"></i>
                           
                            @if ($evaluation->evaluatable_type === 'App\\Models\\Formation')
                                Formation
                            @elseif($evaluation->evaluatable_type === 'App\\Models\\Course')
                                Cours
                            @elseif($evaluation->evaluatable_type === 'App\\Models\\Chapter')
                                Chapitre
                            @endif
                           
                        </div>
                    </div>
                </div>

                <!-- Informations détaillées -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-light rounded">
                        <small class="text-muted">Questions</small>
                            <div class="h5 mb-1">{{ $evaluation->questions->count() }}</div>
                            
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-light rounded">
                         <small class="text-muted">Durée (min)</small>
                            <div class="h5 mb-1">{{ $evaluation->duration ?? 'Illimité' }}</div>
                           
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-light rounded">
                         <small class="text-muted">Tentatives max</small>
                            <div class="h5 mb-1">{{ $evaluation->max_attempts ?? 'Illimité' }}</div>
                            
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="text-center p-3 bg-light rounded">
                         <small class="text-muted">Score requis</small>
                            <div class="h5 mb-1">                   
                                {{ $evaluation->passing_score ?? 60 }}
                                {{$evaluation->scoring_mode == "points" ? " Points" : "  %"}}
                            </div>
                        </div>
                    </div>  
                </div>
            
            </div>
       
        </div>

        <!-- Tentatives précédentes -->
        @if ($attempts->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-clock-history me-2"></i>
                        Vos tentatives précédentes 
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Score</th>
                                    <th>Pourcentage</th>
                                    <th>Résultat</th>
                                    <th>Temps</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($attempts as $attempt)
                                    <tr>
                                        <td>{{ $attempt->created_at?->format('d/m/Y H:i') }}</td>
                                        <td>{{ $attempt->score }} / {{ $attempt->total_points }}</td>
                                        <td>
                                            <span class="badge {{ $attempt->passed ? 'bg-success' : 'bg-danger' }}">
                                                {{ $attempt->pourcentage }}%
                                            </span>
                                        </td>
                                        <td>
                                            @if ($attempt->passed)
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle me-1"></i>Réussi
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    <i class="bi bi-x-circle me-1"></i>Échoué
                                                </span>
                                            @endif
                                        </td>
                                        <td>{{ formateTime($attempt->time_spent)  }}</td>
                                        <td>
                                           
                                                <a href="{{ route('student.evaluations.results', [$evaluation, $attempt]) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye me-1"></i>Voir
                                                </a>

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="row mt-4">
                        <div class="col-12  d-flex justify-content-center">
                        {{-- Afficher les liens de pagination --}}
                            {{ $attempts->links() }}
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Actions -->
        <div class="card">
            <div class="card-body text-center">
                @php
                    $attemptCount = $attempts->count();
                    $canAttempt = !$evaluation->max_attempts || $attemptCount < $evaluation->max_attempts;
                @endphp
                
                @if ($canAttempt)
                    <div class="mb-4">
            
                        <h5 class="mb-2">Commencer l'évaluation</h5>
                        <p class="text-muted mb-3">
                            Testez vos connaissances et mesurez votre progression
                        </p>
                        <a href="{{ route('student.evaluations.start', $evaluation) }}" 
                           class="btn btn-primary btn-lg">
                            <i class="bi bi-play-circle-fill me-2"></i>{{ $attempts->count() > 0 ?  "Retenter" : "Lancer" }}  l'évaluation
                        </a>
                    </div>
                @else
                    <div class="alert alert-warning mb-4">
                        <div class="d-flex align-items-center justify-content-center">
                            <div class="icon-circle bg-warning me-3">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                            </div>
                            <div class="text-start">
                                <h6 class="mb-1">Limite de tentatives atteinte</h6>
                                <p class="mb-0">Vous avez utilisé toutes vos tentatives autorisées ({{ $evaluation->max_attempts }})</p>
                            </div>
                        </div>
                    </div>
                @endif
                
                <div class="d-flex justify-content-center gap-2 flex-wrap">
                    <a href="{{ route('student.evaluations.index') }}" 
                       class="btn btn-outline-secondary">
                        <i class="bi bi-house me-1"></i>Retour au tableau
                    </a>
                    
                    @if ($attempts->count() > 0 && $attempts->last()->completed_at)
                        <a href="{{ route('student.evaluations.results', [$evaluation, $attempts->last()]) }}" 
                           class="btn btn-outline-secondary">
                            <i class="bi bi-award me-1"></i>Derniers résultats
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        .icon-circle {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--bs-primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }

        .icon-circle.bg-success {
            background: var(--bs-success);
        }

        .icon-circle.bg-warning {
            background: var(--bs-warning);
        }
    </style>
@endsection
