@extends('layouts.authenticated.owners.index')
@section('page-title', 'Créer une évaluation - Étape 1')

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
                            <i class="bi bi-clipboard-check me-2"></i>
                            Créer une évaluation - Étape 1/3
                        </h4>
                    </div>
                    <div class="card-body">
                        <!-- Progress bar -->
                        <div class="progress mb-4" style="height: 8px;">
                            <div class="progress-bar" role="progressbar" style="width: 33.33%;" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        
                        <div class="text-center mb-5">
                            <div class="mb-3">
                                <i class="bi bi-lightbulb fa-3x text-warning mb-3"></i>
                            </div>
                            <h5 class="fw-bold">Quel type d'évaluation voulez-vous créer ?</h5>
                            <p class="text-muted fs-6">Sélectionnez le type de contenu auquel l'évaluation sera associée</p>
                        </div>

                        <form action="{{ route('evaluations.store.step1') }}" method="POST">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-4 mb-4">
                                    <div class="card h-100 border-2 border-primary shadow-sm hover-card">
                                        <div class="card-body text-center p-4">
                                            <input type="radio" name="model_type" id="formation" value="App\Models\Formation" class="form-check-input d-none" required>
                                            <label for="formation" class="form-check-label w-100 cursor-pointer">
                                                <div class="mb-3">
                                                    <i class="bi bi-mortarboard-fill fa-4x text-primary"></i>
                                                </div>
                                                <h6 class="fw-bold text-primary">Formation</h6>
                                                <p class="text-muted small mb-0">
                                                    <i class="bi bi-layers me-1"></i>
                                                    Évaluer une formation complète
                                                </p>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4 mb-4">
                                    <div class="card h-100 border-2 shadow-sm hover-card">
                                        <div class="card-body text-center p-4">
                                            <input type="radio" name="model_type" id="course" value="App\Models\Course" class="form-check-input d-none">
                                            <label for="course" class="form-check-label w-100 cursor-pointer">
                                                <div class="mb-3">
                                                    <i class="bi bi-book fa-4x text-secondary"></i>
                                                </div>
                                                <h6 class="fw-bold text-secondary">Cours</h6>
                                                <p class="text-muted small mb-0">
                                                    <i class="bi bi-bookmark me-1"></i>
                                                    Évaluer un cours spécifique
                                                </p>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4 mb-4">
                                    <div class="card h-100 border-2 shadow-sm hover-card">
                                        <div class="card-body text-center p-4">
                                            <input type="radio" name="model_type" id="chapter" value="App\Models\Chapter" class="form-check-input d-none">
                                            <label for="chapter" class="form-check-label w-100 cursor-pointer">
                                                <div class="mb-3">
                                                    <i class="bi bi-file-text fa-4x text-primary"></i>
                                                </div>
                                                <h6 class="fw-bold text-primary">Chapitre</h6>
                                                <p class="text-muted small mb-0">
                                                    <i class="bi bi-file me-1"></i>
                                                    Évaluer un chapitre précis
                                                </p>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @error('model_type')
                                <div class="alert alert-danger d-flex align-items-center">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    <div>{{ $message }}</div>
                                </div>
                            @enderror

                            <div class="d-flex justify-content-between mt-5">
                                <a href="{{ route('evaluations.index') }}" class="btn btn-outline-secondary btn-lg">
                                    <i class="bi bi-arrow-left me-2"></i>
                                    <i class="bi bi-house me-2"></i>Retour
                                </a>
                                <button type="submit" class="btn btn-primary btn-lg" id="nextBtn" disabled>
                                    <i class="bi bi-arrow-right me-2"></i>
                                    Suivant
                                    <i class="bi bi-chevron-right ms-2"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Initialisation au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            // S'assurer que toutes les cartes sont en état non-sélectionné au chargement
            document.querySelectorAll('.card').forEach(card => {
                card.classList.remove('bg-light', 'border-primary', 'shadow-custom');
                card.style.backgroundColor = '';
                card.style.borderColor = '';
                card.style.borderWidth = '';
                card.style.boxShadow = '';
            });
            
            // Désactiver le bouton next au chargement
            const nextBtn = document.getElementById('nextBtn');
            if (nextBtn) {
                nextBtn.disabled = true;
            }
        });

        document.querySelectorAll('input[name="model_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.getElementById('nextBtn').disabled = false;
                
                // Reset all cards to initial state
                document.querySelectorAll('.card').forEach(card => {
                    card.classList.remove('bg-light', 'border-primary', 'shadow-lg');
                    card.style.backgroundColor = '';
                    card.style.borderColor = '';
                    card.style.borderWidth = '';
                    card.style.boxShadow = '';
                });
                
                // Highlight selected card
                const selectedCard = this.closest('.card');
                selectedCard.classList.add('bg-light', 'border-primary', 'shadow-lg');
                selectedCard.style.borderWidth = '5px';
                selectedCard.style.boxShadow = '0 12px 35px rgba(var(--bs-primary-rgb), 0.4), 0 8px 25px rgba(var(--bs-primary-rgb), 0.3), 0 4px 15px rgba(var(--bs-primary-rgb), 0.2)';
            });
        });
    </script>
@endsection
