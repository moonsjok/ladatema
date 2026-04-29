@extends('layouts.authenticated.students.index')
@section('page-title', 'Mes Évaluations')

@section('dashboard-content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-gradient-primary text-white">
                        <h4 class="mb-0">
                            <i class="bi bi-clipboard-check me-2"></i>
                            Mes Évaluations Disponibles
                        </h4>
                    </div>
                    <div class="card-body">
                        @if($evaluations->isEmpty())
                            <div class="text-center py-5">
                                <i class="bi bi-inbox fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">Aucune évaluation disponible</h5>
                                <p class="text-muted">
                                    Vous n'avez pas encore accès à des évaluations. 
                                    Souscrivez à des formations pour débloquer des évaluations.
                                </p>
                                <a href="{{ route('student.formations.index') }}" class="btn btn-primary">
                                    <i class="bi bi-book me-2"></i>
                                    Voir les formations
                                </a>
                            </div>
                        @else
                            <div class="row">
                                @foreach($evaluations as $evaluation)
                                    <div class="col-md-6 col-lg-4 mb-4">
                                        <div class="card h-100 border-2 shadow-sm hover-card">
                                            <div class="card-body">
                                                <!-- Badge de type -->
                                                <div class="mb-3">
                                                    @if($evaluation->evaluatable_type == 'App\Models\Formation')
                                                        <span class="badge bg-primary fs-6">
                                                            <i class="bi bi-mortarboard me-1"></i>Formation
                                                        </span>
                                                    @elseif($evaluation->evaluatable_type == 'App\Models\Course')
                                                        <span class="badge bg-secondary fs-6">
                                                            <i class="bi bi-book me-1"></i>Cours
                                                        </span>
                                                    @else
                                                        <span class="badge bg-info fs-6">
                                                            <i class="bi bi-file-text me-1"></i>Chapitre
                                                        </span>
                                                    @endif
                                                </div>
                                                
                                                <h6 class="card-title fw-bold mb-2">{{ $evaluation->title }}</h6>
                                                
                                                <p class="card-text text-muted small mb-3">
                                                    {{ Str::limit($evaluation->description, 80) }}
                                                </p>
                                                
                                                <!-- Informations sur l'évaluation -->
                                                <div class="evaluation-info mb-3">
                                                  
                                                        <div class="d-flex align-items-center mb-2">
                                                            <i class="bi bi-clock me-2 text-primary"></i>
                                                            <small class="text-muted">
                                                                Durée : {{ !empty($evaluation->duration) ? formateTime($evaluation->duration  * 60) : "Illimité"  }} 
                                                            </small>
                                                        </div>
                
                                                        <div class="d-flex align-items-center mb-2">
                                                            <i class="bi bi-question-circle me-2 text-primary"></i>
                                                            <small class="text-muted">
                                                                Questions : {{ $evaluation->total_questions }} 
                                                            </small>
                                                        </div>
                                                    
                                                    <div class="d-flex align-items-center mb-2">
                                                        <i class="bi bi-award me-2 text-primary"></i>
                                                        <small class="text-muted">
                                                            Mode : {{ $evaluation->scoring_mode == 'pourcentage' ? 'Pourcentage' : 'Points' }}
                                                        </small>
                                                    </div>
                                                    
                                                    <div class="d-flex align-items-center mb-2">
                                                        <i class="bi bi-exclamation-triangle me-2 text-{{ $evaluation->importance_color }}"></i>
                                                        <small class="text-muted">
                                                            Importance : {{ $evaluation->importance_label }}
                                                        </small>
                                                    </div>
                                                    
                                                    @if($evaluation->passing_score)
                                                        <div class="d-flex align-items-center">
                                                            <i class="bi bi-trophy me-2 text-primary"></i>
                                                            <small class="text-muted">
                                                                Minimum : {{ $evaluation->passing_score }} 
                                                                {{ $evaluation->scoring_mode == 'pourcentage' ? '%' : 'points' }}
                                                            </small>
                                                        </div>
                                                    @endif
                                                </div>
                                                
                                                <div class="d-grid gap-2">
                                                    <a href="{{ route('student.evaluations.show', $evaluation) }}" 
                                                       class="btn btn-outline-primary btn-sm">
                                                        <i class="bi bi-eye me-1"></i>
                                                        Détails
                                                    </a>
                                                    <a href="{{ route('student.evaluations.start', $evaluation) }}" 
                                                       class="btn btn-primary btn-sm">
                                                        <i class="bi bi-play-circle me-1"></i>
                                                        Commencer
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .evaluation-info {
            font-size: 0.85rem;
        }
        
        .evaluation-info i {
            width: 16px;
        }
        
        .hover-card {
            transition: all 0.3s ease;
        }
        
        .hover-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
    </style>
@endsection
