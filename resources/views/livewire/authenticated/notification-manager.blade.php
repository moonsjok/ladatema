<div id="notification-manager-root" class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-primary text-white py-3 rounded-top-4">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 fs-6">
                <i class="bi bi-send-plus-fill me-2"></i> Assistant d'envoi de notification
            </h5>
            <span class="badge bg-white text-primary rounded-pill px-3 py-1.5 fw-semibold">
                Étape {{ $step }} / 3
            </span>
        </div>
    </div>

    <div class="card-body p-4">
        @if($successMessage)
            <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-check-circle-fill fs-4 me-3 text-success"></i>
                    <div>
                        <h6 class="fw-bold mb-0">Succès !</h6>
                        <p class="mb-0 small">{{ $successMessage }}</p>
                    </div>
                </div>
                <button type="button" class="btn-close" wire:click="dismissSuccess" aria-label="Close"></button>
            </div>
        @endif

        <!-- Barre de progression des étapes -->
        <div class="position-relative mb-4 pb-2">
            <div class="progress" style="height: 4px;">
                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ ($step / 3) * 100 }}%;" aria-valuenow="{{ $step }}" aria-valuemin="1" aria-valuemax="3"></div>
            </div>
            <div class="d-flex justify-content-between position-relative mt-n2 px-2" style="margin-top: -14px;">
                <button class="btn btn-sm rounded-circle {{ $step >= 1 ? 'btn-primary' : 'btn-secondary opacity-50' }} fw-bold" style="width: 28px; height: 28px; padding: 0;">1</button>
                <button class="btn btn-sm rounded-circle {{ $step >= 2 ? 'btn-primary' : 'btn-secondary opacity-50' }} fw-bold" style="width: 28px; height: 28px; padding: 0;">2</button>
                <button class="btn btn-sm rounded-circle {{ $step >= 3 ? 'btn-primary' : 'btn-secondary opacity-50' }} fw-bold" style="width: 28px; height: 28px; padding: 0;">3</button>
            </div>
            <div class="d-flex justify-content-between small text-muted mt-2 fw-semibold px-1" style="font-size: 0.78rem;">
                <span class="{{ $step === 1 ? 'text-primary fw-bold' : '' }}">1. Destinataire</span>
                <span class="{{ $step === 2 ? 'text-primary fw-bold' : '' }}">2. Message</span>
                <span class="{{ $step === 3 ? 'text-primary fw-bold' : '' }}">3. Confirmation</span>
            </div>
        </div>

        <!-- ÉTAPE 1 : Choix du destinataire -->
        @if($step === 1)
            <div class="step-content">
                <h6 class="fw-bold text-dark mb-3">
                    <i class="bi bi-people-fill text-primary me-2"></i> Qui doit recevoir cette notification ?
                </h6>

                <div class="row g-2 mb-3">
                    <!-- Option All -->
                    <div class="col-12">
                        <div class="card border p-3 rounded-3 cursor-pointer shadow-sm-hover {{ $target_type === 'all' ? 'border-primary bg-primary-subtle' : '' }}"
                             wire:click="$set('target_type', 'all')">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="target_type" id="target_all" value="all" wire:model.live="target_type">
                                <label class="form-check-label fw-bold text-dark" for="target_all">
                                    <i class="bi bi-globe me-2 text-primary"></i> Tous les utilisateurs (Global)
                                </label>
                                <div class="small text-muted ps-4">
                                    Envoyé à l'ensemble des étudiants, enseignants et administrateurs.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Option Student -->
                    <div class="col-md-6">
                        <div class="card border p-3 rounded-3 cursor-pointer shadow-sm-hover h-100 {{ $target_type === 'student' ? 'border-primary bg-primary-subtle' : '' }}"
                             wire:click="$set('target_type', 'student')">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="target_type" id="target_student" value="student" wire:model.live="target_type">
                                <label class="form-check-label fw-bold text-dark" for="target_student">
                                    <i class="bi bi-mortarboard-fill me-2 text-success"></i> Tous les Étudiants
                                </label>
                                <div class="small text-muted ps-4">
                                    Réservé uniquement aux comptes étudiants.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Option Owner -->
                    <div class="col-md-6">
                        <div class="card border p-3 rounded-3 cursor-pointer shadow-sm-hover h-100 {{ $target_type === 'owner' ? 'border-primary bg-primary-subtle' : '' }}"
                             wire:click="$set('target_type', 'owner')">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="target_type" id="target_owner" value="owner" wire:model.live="target_type" {{ $authUser->role === 'owner' ? 'disabled' : '' }}>
                                <label class="form-check-label fw-bold text-dark" for="target_owner">
                                    <i class="bi bi-shield-lock-fill me-2 text-warning"></i> Tous les Propriétaires / Admin
                                </label>
                                <div class="small text-muted ps-4">
                                    @if($authUser->role === 'owner')
                                        <span class="text-danger">Réservé au rôle Développeur.</span>
                                    @else
                                        Administrateurs et gestionnaires de la plateforme.
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Option Dev (uniquement pour dev) -->
                    @if($authUser->role === 'dev')
                        <div class="col-md-6">
                            <div class="card border p-3 rounded-3 cursor-pointer shadow-sm-hover h-100 {{ $target_type === 'dev' ? 'border-primary bg-primary-subtle' : '' }}"
                                 wire:click="$set('target_type', 'dev')">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="target_type" id="target_dev" value="dev" wire:model.live="target_type">
                                    <label class="form-check-label fw-bold text-dark" for="target_dev">
                                        <i class="bi bi-code-slash me-2 text-danger"></i> Tous les Développeurs
                                    </label>
                                    <div class="small text-muted ps-4">
                                        Équipe technique et développeurs.
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Option User spécifique -->
                    <div class="col-12">
                        <div class="card border p-3 rounded-3 cursor-pointer shadow-sm-hover {{ $target_type === 'user' ? 'border-primary bg-primary-subtle' : '' }}"
                             wire:click="$set('target_type', 'user')">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="target_type" id="target_user" value="user" wire:model.live="target_type">
                                <label class="form-check-label fw-bold text-dark" for="target_user">
                                    <i class="bi bi-person-fill me-2 text-info"></i> Un utilisateur spécifique
                                </label>
                                <div class="small text-muted ps-4">
                                    Rechercher un utilisateur individuel par son nom ou e-mail.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panneau de recherche pour un utilisateur spécifique -->
                @if($target_type === 'user')
                    <div class="card border-info p-3 mb-3 bg-white rounded-3 shadow-sm">
                        <label class="form-label fw-bold text-dark">
                            <i class="bi bi-search me-1 text-primary"></i> Rechercher l'utilisateur destinataire :
                        </label>

                        @if($selectedUser)
                            <!-- Utilisateur sélectionné -->
                            <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded-3 border border-success">
                                <div class="d-flex align-items-center">
                                    <div class="btn btn-sm btn-success rounded-circle me-3 p-2">
                                        <i class="bi bi-check-lg fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $selectedUser->prenoms }} {{ $selectedUser->nom }} ({{ $selectedUser->name }})</div>
                                        <div class="small text-muted">{{ $selectedUser->email }} • Badge: <span class="badge bg-secondary">{{ ucfirst($selectedUser->role) }}</span></div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill" wire:click="clearSelectedUser">
                                    <i class="bi bi-x-circle me-1"></i> Changer
                                </button>
                            </div>
                        @else
                            <!-- Champ de recherche -->
                            <input type="text" class="form-control rounded-pill px-3"
                                   placeholder="Tapez un nom, prénom ou email..."
                                   wire:model.live.debounce.300ms="searchUser">
                            
                            @error('target_user_id')
                                <div class="text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i> {{ $message }}</div>
                            @enderror

                            <!-- Résultats de recherche -->
                            @if(!empty($searchUser))
                                <div class="list-group mt-2 shadow-sm rounded-3 overflow-hidden" style="max-height: 250px; overflow-y: auto;">
                                    @forelse($searchResults as $u)
                                        <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2"
                                                wire:click="selectUser({{ $u->id }})">
                                            <div>
                                                <div class="fw-bold text-dark">{{ $u->prenoms }} {{ $u->nom }} ({{ $u->name }})</div>
                                                <div class="small text-muted">{{ $u->email }}</div>
                                            </div>
                                            <span class="badge bg-primary rounded-pill">
                                                <i class="bi bi-plus-lg me-1"></i> Choisir ({{ ucfirst($u->role) }})
                                            </span>
                                        </button>
                                    @empty
                                        <div class="list-group-item text-muted text-center py-3">
                                            Aucun utilisateur trouvé pour "{{ $searchUser }}".
                                        </div>
                                    @endforelse
                                </div>
                            @endif
                        @endif
                    </div>
                @endif

                @error('target_type')
                    <div class="alert alert-danger p-2 small mb-3">{{ $message }}</div>
                @enderror

                <!-- Boutons d'action Étape 1 -->
                <div class="d-flex justify-content-end mt-4">
                    <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold" 
                            wire:click="goToStep(2)" 
                            {{ ($target_type === 'user' && !$target_user_id) ? 'disabled' : '' }}>
                        Étape 2 : Message <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </div>
            </div>
        @endif

        <!-- ÉTAPE 2 : Contenu du message & Options -->
        @if($step === 2)
            <div class="step-content">
                <h6 class="fw-bold text-dark mb-3">
                    <i class="bi bi-pencil-square text-primary me-2"></i> Rédiger la notification
                </h6>

                <!-- Titre -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Titre du message <span class="text-danger">*</span></label>
                    <input type="text" class="form-control rounded-3 @error('title') is-invalid @enderror" 
                           placeholder="Ex: Maintenance programmée, Nouvelle formation disponible..." 
                           wire:model="title">
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Contenu -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Contenu du message <span class="text-danger">*</span></label>
                    <textarea class="form-control rounded-3 @error('message') is-invalid @enderror" 
                              rows="5" 
                              placeholder="Rédigez le texte complet de votre notification..." 
                              wire:model="message"></textarea>
                    @error('message')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Switches d'options -->
                <div class="card border-0 bg-light p-3 rounded-3 mb-3">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" role="switch" id="is_important" wire:model="is_important">
                        <label class="form-check-label fw-bold text-dark" for="is_important">
                            <i class="bi bi-exclamation-triangle-fill text-danger me-1"></i> Marquer comme Message Important
                        </label>
                        <div class="small text-muted">Affichera une bannière d'alerte rouge prioritaire en haut du tableau de bord.</div>
                    </div>

                    <div class="form-check form-switch border-top pt-2 mt-2">
                        <input class="form-check-input" type="checkbox" role="switch" id="send_email" wire:model="send_email">
                        <label class="form-check-label fw-bold text-dark" for="send_email">
                            <i class="bi bi-envelope-at-fill text-primary me-1"></i> Envoyer également par E-mail
                        </label>
                        <div class="small text-muted">Un e-mail de notification sera expédié au(x) destinataire(s).</div>
                    </div>
                </div>

                <!-- Boutons d'action Étape 2 -->
                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-semibold" wire:click="goToStep(1)">
                        <i class="bi bi-arrow-left me-1"></i> Retour
                    </button>
                    <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold" wire:click="goToStep(3)">
                        Étape 3 : Aperçu & Confirmation <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </div>
            </div>
        @endif

        <!-- ÉTAPE 3 : Aperçu & Confirmation -->
        @if($step === 3)
            <div class="step-content">
                <h6 class="fw-bold text-dark mb-3">
                    <i class="bi bi-eye-fill text-primary me-2"></i> Vérification avant publication
                </h6>

                <!-- Récapitulatif destinataire -->
                <div class="card border-0 bg-light p-3 rounded-3 mb-3">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <span class="small text-muted d-block fw-semibold">Cible des destinataires :</span>
                            <span class="fw-bold text-dark fs-6">
                                @if($target_type === 'all')
                                    🌐 Tous les utilisateurs (Global)
                                @elseif($target_type === 'student')
                                    🎓 Tous les Étudiants
                                @elseif($target_type === 'owner')
                                    🛡️ Tous les Propriétaires / Admin
                                @elseif($target_type === 'dev')
                                    💻 Tous les Développeurs
                                @elseif($target_type === 'user' && $selectedUser)
                                    👤 {{ $selectedUser->prenoms }} {{ $selectedUser->nom }} ({{ $selectedUser->email }})
                                @endif
                            </span>
                        </div>
                        <div class="col-md-6 mb-2">
                            <span class="small text-muted d-block fw-semibold">Options sélectionnées :</span>
                            <span class="badge bg-{{ $is_important ? 'danger' : 'info' }} me-1">
                                {{ $is_important ? '🚨 Important (Rouge)' : '📌 Normal (Bleu)' }}
                            </span>
                            @if($send_email || ($target_type === 'user' && $target_user_id))
                                <span class="badge bg-success"><i class="bi bi-envelope-check me-1"></i> E-mail activé</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Aperçu exact du rendu -->
                <div class="mb-3">
                    <label class="form-label small text-muted fw-bold">Aperçu exact du bandeau notification sur le tableau de bord :</label>
                    <div class="alert alert-{{ $is_important ? 'danger' : 'info' }} shadow-sm rounded-4 border-0 p-3 mb-0">
                        <div class="d-flex align-items-start">
                            <div class="me-3 fs-3 text-{{ $is_important ? 'danger' : 'info' }}">
                                <i class="bi {{ $is_important ? 'bi-exclamation-triangle-fill' : 'bi-bell-fill' }}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-1">
                                    <h5 class="alert-heading fw-bold mb-0 me-2">{{ $title }}</h5>
                                    @if($is_important)
                                        <span class="badge bg-danger rounded-pill">Important</span>
                                    @endif
                                </div>
                                <p class="mb-0 text-dark" style="white-space: pre-line;">{{ $message }}</p>
                                <small class="text-muted d-block mt-1" style="font-size: 0.8rem;">
                                    <i class="bi bi-clock me-1"></i> De : {{ $authUser->name }} (À l'instant)
                                </small>
                            </div>
                            <div>
                                <span class="btn btn-sm btn-outline-secondary rounded-pill disabled opacity-75">
                                    <i class="bi bi-check-lg me-1"></i> Marquer comme lu
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                @error('send_error')
                    <div class="alert alert-danger p-2 small mb-3">{{ $message }}</div>
                @enderror

                <!-- Boutons d'action Étape 3 -->
                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-semibold" wire:click="goToStep(2)">
                        <i class="bi bi-arrow-left me-1"></i> Modifier le message
                    </button>
                    <button type="button" class="btn btn-success btn-lg rounded-pill px-5 fw-bold shadow-sm" 
                            wire:click="sendNotification" 
                            wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="sendNotification">
                            <i class="bi bi-send-fill me-1"></i> Confirmer & Envoyer
                        </span>
                        <span wire:loading wire:target="sendNotification">
                            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                            Envoi en cours...
                        </span>
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>
