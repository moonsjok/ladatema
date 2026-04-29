@extends('layouts.authenticated.owners.index')
@section('page-title', 'Modifier une évaluation - Étape 3')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/evaluation-creation.css') }}">
@endpush

@section('dashboard-content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-gradient-primary text-white">
                        <h4 class="mb-0">
                            <i class="bi bi-pencil-square me-2"></i>
                            Modifier une évaluation - Étape 3/3
                        </h4>
                    </div>
                    <div class="card-body">
                        <!-- Progress bar -->
                        <div class="progress mb-4" style="height: 8px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        
                        <!-- Résumé de la sélection actuelle -->
                        <div class="card border-light bg-light mb-4">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="bi bi-pencil-square me-2 text-warning"></i>
                                    Modifications en cours
                                </h5>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="summary-item">
                                            <small class="text-muted">Type</small>
                                            <div class="summary-value">
                                                @if($sessionData['new_model_type'] == 'App\Models\Formation')
                                                    <span class="badge bg-primary">
                                                        <i class="bi bi-mortarboard-fill me-1"></i>Formation
                                                    </span>
                                                @elseif($sessionData['new_model_type'] == 'App\Models\Course')
                                                    <span class="badge bg-secondary">
                                                        <i class="bi bi-book me-1"></i>Cours
                                                    </span>
                                                @else
                                                    <span class="badge bg-info">
                                                        <i class="bi bi-file-text me-1"></i>Chapitre
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="summary-item">
                                            <small class="text-muted">Nouvel élément</small>
                                            <div class="summary-value">
                                                <strong class="text-primary">{{ $model->title }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                @if($model->description)
                                    <div class="mt-3 pt-3 border-top">
                                        <small class="text-muted">Description</small>
                                        <p class="summary-description">{{ Str::limit($model->description, 150) }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="text-center mb-5">
                            <div class="mb-3">
                                <i class="bi bi-flag-fill fa-3x text-success mb-3"></i>
                            </div>
                            <h5 class="fw-bold">Dernière étape : Valider les modifications</h5>
                            <p class="text-muted fs-6">Confirmez et mettez à jour les informations de l'évaluation</p>
                        </div>

                        <form action="{{ route('evaluations.update.step3', $evaluation) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-4">
                                        <label for="title" class="form-label fw-bold">
                                            <i class="bi bi-type me-2 text-primary"></i>
                                            Titre de l'évaluation
                                        </label>
                                        <input type="text" class="form-control form-control-lg" id="title" name="title" 
                                               value="{{ old('title', $evaluation->title) }}" required>
                                        <div class="form-text">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Un titre clair et descriptif pour votre évaluation
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-12">
                                    <div class="mb-4">
                                        <label for="description" class="form-label fw-bold">
                                            <i class="bi bi-text-left me-2 text-primary"></i>
                                            Description
                                        </label>
                                        <textarea class="form-control" id="description" name="description" rows="4" 
                                                  placeholder="Décrivez l'objectif de cette évaluation...">{{ old('description', $evaluation->description) }}</textarea>
                                        <div class="form-text">
                                            <i class="bi bi-lightbulb me-1"></i>
                                            Optionnel : expliquez ce que cette évaluation va tester
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="scoring_mode" class="form-label fw-bold">
                                            <i class="bi bi-calculator me-2 text-primary"></i>
                                            Mode de notation
                                        </label>
                                        <select class="form-select" id="scoring_mode" name="scoring_mode">
                                            <option value="pourcentage" {{ old('scoring_mode', $evaluation->scoring_mode ?? 'pourcentage') == 'pourcentage' ? 'selected' : '' }}>
                                                Pourcentage (%)
                                            </option>
                                            <option value="points" {{ old('scoring_mode', $evaluation->scoring_mode ?? 'pourcentage') == 'points' ? 'selected' : '' }}>
                                                Points
                                            </option>
                                        </select>
                                        <div class="form-text">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Mode de calcul du score (défaut: pourcentage)
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="passing_score" class="form-label fw-bold">
                                            <i class="bi bi-trophy me-2 text-primary"></i>
                                            Score de réussite
                                        </label>
                                        <input type="number" class="form-control" id="passing_score" name="passing_score" 
                                               value="{{ old('passing_score', $evaluation->passing_score ?? 60) }}" min="0" max="100">
                                        <div class="form-text">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Score minimum pour réussir  (défaut: 60 % ou 60 points)
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="duration" class="form-label fw-bold">
                                            <i class="bi bi-clock me-2 text-primary"></i>
                                            Durée (minutes)
                                        </label>
                                        <input type="number" class="form-control" id="duration" name="duration" 
                                               value="{{ old('duration', $evaluation->duration) }}" min="1" placeholder="Illimité si vide">
                                        <div class="form-text">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Durée en minutes (laissez vide pour illimité)
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="total_questions" class="form-label fw-bold">
                                            <i class="bi bi-list-ol me-2 text-primary"></i>
                                            Nombre de question
                                        </label>
                                        <input type="number" class="form-control" id="total_questions" name="total_questions" 
                                               value="{{ old('total_questions', $evaluation->total_questions) }}" min="1" placeholder="Toutes si vide">
                                        <div class="form-text">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Nombre de question à afficher (laissez vide pour toutes les {{$evaluation->questions->count()}} questions) 
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="max_attempts" class="form-label fw-bold">
                                            <i class="bi bi-arrow-repeat me-2 text-primary"></i>
                                            Nombre maximum de tentatives
                                        </label>
                                        <input type="number" class="form-control" id="max_attempts" name="max_attempts" 
                                                   value="{{ old('max_attempts', $evaluation->max_attempts) }}" min="1" max="10" placeholder="Illimité si vide">
                                        <div class="form-text">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Nombre de fois qu'un étudiant peut passer cette évaluation (défaut: illimité)
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="importance" class="form-label fw-bold">
                                            <i class="bi bi-exclamation-triangle me-2 text-primary"></i>
                                            Importance de l'évaluation
                                        </label>
                                        <select class="form-select" id="importance" name="importance">
                                            <option value="optional" {{ old('importance', $evaluation->importance) === 'optional' ? 'selected' : '' }}>
                                                Facultative
                                            </option>
                                            <option value="mandatory" {{ old('importance', $evaluation->importance) === 'mandatory' ? 'selected' : '' }}>
                                                Obligatoire
                                            </option>
                                        </select>
                                        <div class="form-text">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Obligatoire : les étudiants doivent la passer | Facultative : les étudiants peuvent la sauter
                                        </div>
                                    </div>
                                </div>
                                </div>
                                



                            </div>

                            
                            @error('title')
                                <div class="alert alert-danger d-flex align-items-center">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    <div>{{ $message }}</div>
                                </div>
                            @enderror

                            <div class="d-flex justify-content-between mt-5">
                                <a href="{{ route('evaluations.edit.step2', $evaluation) }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>
                                    Précédent
                                </a>
                                <div>
                                    <a href="{{ route('evaluations.show', $evaluation) }}" class="btn btn-outline-danger me-2">
                                        <i class="bi bi-x-circle me-2"></i>
                                        Annuler
                                    </a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-check-circle me-2"></i>
                                        Mettre à jour l'évaluation
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
