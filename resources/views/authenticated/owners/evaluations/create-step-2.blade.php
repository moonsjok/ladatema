@extends('layouts.authenticated.owners.index')
@section('page-title', 'Créer une évaluation - Étape 2')

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
                            Créer une évaluation - Étape 2/3
                        </h4>
                    </div>
                    <div class="card-body">
                        <!-- Progress bar -->
                        <div class="progress mb-4" style="height: 8px;">
                            <div class="progress-bar" role="progressbar" style="width: 66.66%;" aria-valuenow="66" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        
                        <div class="text-center mb-5">
                            <div class="mb-3">
                                <i class="bi bi-search fa-3x text-primary mb-3"></i>
                            </div>
                            <h5 class="fw-bold">Sélectionnez l'élément à évaluer</h5>
                            <p class="text-muted fs-6">
                                Type sélectionné : 
                                @if($modelType == 'App\Models\Formation')
                                    <span class="badge bg-primary fs-6">
                                        <i class="bi bi-mortarboard-fill me-1"></i>Formation
                                    </span>
                                @elseif($modelType == 'App\Models\Course')
                                    <span class="badge bg-secondary fs-6">
                                        <i class="bi bi-book me-1"></i>Cours
                                    </span>
                                @else
                                    <span class="badge bg-info fs-6">
                                        <i class="bi bi-file-text me-1"></i>Chapitre
                                    </span>
                                @endif
                            </p>
                        </div>

                        <!-- En-tête -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="mb-1 fw-bold">
                                    <i class="bi bi-list-ul me-2 text-primary"></i>
                                    Sélectionner un élément
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

                        <form action="{{ route('evaluations.store.step2') }}" method="POST" id="selectionForm">
                            @csrf
                            
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
                                                    <input type="radio" name="model_id" id="item_{{ $item->id }}" value="{{ $item->id }}" class="form-check-input" required>
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
                                        <div class="alert alert-info d-flex align-items-center">
                                            <i class="bi bi-search fa-2x me-3"></i>
                                            <div>
                                                <h6 class="alert-heading mb-1">Aucun élément trouvé</h6>
                                                <p class="mb-0">Aucun élément ne correspond à votre recherche pour ce type.</p>
                                            </div>
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

                            @error('model_id')
                                <div class="alert alert-danger d-flex align-items-center mt-3">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    <div>{{ $message }}</div>
                                </div>
                            @enderror

                            <div class="d-flex justify-content-between mt-5">
                                <a href="{{ route('evaluations.create.step1') }}" class="btn btn-outline-secondary btn-lg">
                                    <i class="bi bi-arrow-left me-2"></i>
                                    <i class="bi bi-skip-backward me-2"></i>Précédent
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
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const itemCards = document.querySelectorAll('.item-card');
            const nextBtn = document.getElementById('nextBtn');
            const radios = document.querySelectorAll('input[name="model_id"]');

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
                
                const itemTitle = document.querySelector(`#item_${itemId}`).closest('.selection-label').querySelector('.selection-title').textContent;
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
                .result-counter {
                    margin-top: 10px;
                    padding: 8px 12px;
                    border-radius: 6px;
                    font-weight: 600;
                    animation: slideIn 0.3s ease;
                }
                .result-counter.text-success {
                    background-color: var(--bs-success-bg-subtle);
                    color: var(--bs-success-text);
                    border-left: 3px solid var(--bs-success);
                }
                .result-counter.text-danger {
                    background-color: var(--bs-danger-bg-subtle);
                    color: var(--bs-danger-text);
                    border-left: 3px solid var(--bs-danger);
                }
            `;
            document.head.appendChild(style);

            // Initialiser le compteur au chargement
            updateResultCount(itemCards.length);
        });
    </script>
@endsection
