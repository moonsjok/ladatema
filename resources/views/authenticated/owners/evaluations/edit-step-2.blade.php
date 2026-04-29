@extends('layouts.authenticated.owners.index')
@section('page-title', 'Modifier une évaluation - Étape 2')

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
                            Modifier une évaluation - Étape 2/3
                        </h4>
                    </div>
                    <div class="card-body">
                        <!-- Progress bar -->
                        <div class="progress mb-4" style="height: 8px;">
                            <div class="progress-bar" role="progressbar" style="width: 66.66%;" aria-valuenow="66" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        
                        <!-- Informations actuelles -->
                        <div class="alert alert-info d-flex align-items-center mb-4">
                            <i class="bi bi-info-circle me-2"></i>
                            <div>
                                <strong>Évaluation :</strong> {{ $evaluation->title }}<br>
                                <small>Nouveau type sélectionné : 
                                    @if($modelType == 'App\Models\Formation')
                                        <span class="badge bg-primary">Formation</span>
                                    @elseif($modelType == 'App\Models\Course')
                                        <span class="badge bg-secondary">Cours</span>
                                    @else
                                        <span class="badge bg-primary">Chapitre</span>
                                    @endif
                                </small>
                            </div>
                        </div>

                        <!-- En-tête -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="mb-1 fw-bold">
                                    <i class="bi bi-list-ul me-2 text-primary"></i>
                                    Sélectionner un nouvel élément
                                </h5>
                            </div>
                            <div class="text-muted small">
                                <i class="bi bi-info-circle me-1"></i>
                                Total : {{ $items->total() }} éléments
                            </div>
                        </div>

                        <!-- Barre de recherche améliorée -->
                        <div class="mb-4">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-primary text-white">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" id="searchInput" class="form-control border-primary" placeholder="Rechercher un élément...">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-funnel text-muted"></i>
                                </span>
                            </div>
                            <div class="form-text mt-2">
                                <i class="bi bi-info-circle me-1"></i>
                                Tapez pour rechercher instantanément
                            </div>
                        </div>

                        <form action="{{ route('evaluations.update.step2', $evaluation) }}" method="POST" id="selectionForm">
                            @csrf
                            @method('PUT')
                            
                            <!-- Liste des éléments -->
                            <div class="row" id="itemsList">
                                @forelse($items as $item)
                                    <div class="col-md-6 mb-4 item-card" data-title="{{ strtolower($item->title) }}">
                                        <div class="card h-100 border-2 shadow-sm hover-card selection-card">
                                            <div class="card-body p-4 position-relative">
                                                <!-- Badge de sélection -->
                                                <div class="selection-indicator position-absolute top-0 end-0 m-2">
                                                    <span class="badge bg-success rounded-circle p-2 d-none">
                                                        <i class="bi bi-check-lg text-white"></i>
                                                    </span>
                                                </div>
                                                
                                                <div class="form-check">
                                                    <input type="radio" name="model_id" id="item_{{ $item->id }}" value="{{ $item->id }}" class="form-check-input" required
                                                           @if($item->id == $currentModelId) checked @endif>
                                                    <label for="item_{{ $item->id }}" class="form-check-label w-100 cursor-pointer selection-label">
                                                        <div class="d-flex align-items-start">
                                                            <div class="me-3">
                                                                <div class="icon-wrapper bg-primary bg-opacity-10 p-3 rounded-circle selection-icon">
                                                                    @if($modelType == 'App\Models\Formation')
                                                                        <i class="bi bi-mortarboard-fill fa-2x text-primary"></i>
                                                                    @elseif($modelType == 'App\Models\Course')
                                                                        <i class="bi bi-book fa-2x text-secondary"></i>
                                                                    @else
                                                                        <i class="bi bi-file-text fa-2x text-info"></i>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <h6 class="fw-bold mb-2 selection-title">{{ $item->title }}</h6>
                                                                @if($item->description)
                                                                    <p class="text-muted small mb-0 selection-description">
                                                                        <i class="bi bi-text-left me-1"></i>
                                                                        {{ Str::limit($item->description, 120) }}
                                                                    </p>
                                                                @else
                                                                    <p class="text-muted small mb-0 selection-description">
                                                                        <i class="bi bi-info-circle me-1"></i>
                                                                        Aucune description disponible
                                                                    </p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <div class="alert alert-warning text-center">
                                            <i class="bi bi-exclamation-triangle me-2"></i>
                                            Aucun élément trouvé pour ce type.
                                        </div>
                                    </div>
                                @endforelse
                            </div>

                            <!-- Pagination -->
                            @if($items->hasPages())
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $items->links() }}
                                </div>
                            @endif

                            <div class="d-flex justify-content-between mt-5">
                                <a href="{{ route('evaluations.edit.step1', $evaluation) }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>
                                    Précédent
                                </a>
                                <button type="submit" class="btn btn-primary" id="nextBtn" disabled>
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
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const itemCards = document.querySelectorAll('.item-card');
            const nextBtn = document.getElementById('nextBtn');
            const radios = document.querySelectorAll('input[name="model_id"]');

            // Initialisation au chargement de la page
            const selectedRadio = document.querySelector('input[name="model_id"]:checked');
            if (selectedRadio) {
                nextBtn.disabled = false;
                
                // Masquer tous les indicateurs de sélection
                document.querySelectorAll('.selection-indicator .badge').forEach(badge => {
                    badge.classList.add('d-none');
                });
                
                // Réinitialiser toutes les cartes
                document.querySelectorAll('.selection-card').forEach(card => {
                    card.style.borderColor = '';
                    card.style.background = '';
                });
                
                // Afficher l'indicateur pour l'élément actuellement sélectionné
                const selectedCard = selectedRadio.closest('.selection-card');
                const indicator = selectedRadio.closest('.selection-label').querySelector('.selection-indicator .badge');
                
                if (selectedCard && indicator) {
                    indicator.classList.remove('d-none');
                    selectedCard.style.borderColor = 'var(--primary-color)';
                    selectedCard.style.background = 'linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%)';
                }
            }

            // Gestion de la sélection simplifiée
            radios.forEach(radio => {
                radio.addEventListener('change', function() {
                    nextBtn.disabled = false;
                    
                    // Masquer tous les indicateurs de sélection
                    document.querySelectorAll('.selection-indicator .badge').forEach(badge => {
                        badge.classList.add('d-none');
                    });
                    
                    // Réinitialiser toutes les cartes
                    document.querySelectorAll('.selection-card').forEach(card => {
                        card.style.borderColor = '';
                        card.style.background = '';
                    });
                    
                    // Afficher l'indicateur pour l'élément sélectionné
                    const selectedCard = this.closest('.selection-card');
                    const indicator = this.closest('.selection-label').querySelector('.selection-indicator .badge');
                    
                    if (selectedCard && indicator) {
                        indicator.classList.remove('d-none');
                        selectedCard.style.borderColor = 'var(--primary-color)';
                        selectedCard.style.background = 'linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%)';
                        
                        // Afficher un message de confirmation
                        showSelectionMessage(this.value);
                    }
                });
            });

            // Recherche en temps réel avec animation
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                
                itemCards.forEach((card, index) => {
                    const title = card.dataset.title;
                    if (title.includes(searchTerm)) {
                        card.style.display = 'block';
                        card.style.animation = `slideIn 0.3s ease ${index * 0.05}s both`;
                    } else {
                        card.style.display = 'none';
                    }
                });
            });

            // Fonction pour afficher le message de sélection
            function showSelectionMessage(itemId) {
                // Créer ou mettre à jour le message
                let message = document.querySelector('.selection-message');
                if (!message) {
                    message = document.createElement('div');
                    message.className = 'selection-message';
                    document.body.appendChild(message);
                }
                
                // Récupérer le titre de l'élément sélectionné
                const selectedRadio = document.querySelector(`input[name="model_id"][value="${itemId}"]`);
                const itemTitle = selectedRadio.closest('.selection-card').querySelector('.selection-title').textContent;
                
                message.innerHTML = `
                    <i class="bi bi-check-circle me-2"></i>
                    <strong>Élément sélectionné :</strong> ${itemTitle}
                `;
                message.style.display = 'block';
                
                // Masquer automatiquement après 3 secondes
                setTimeout(() => {
                    message.style.display = 'none';
                }, 3000);
            }

            // Ajouter les animations CSS
            const style = document.createElement('style');
            style.textContent = `
                @keyframes fadeIn {
                    from { opacity: 0; transform: translateY(10px); }
                    to { opacity: 1; transform: translateY(0); }
                }
            `;
            document.head.appendChild(style);
        });
    </script>
@endsection
