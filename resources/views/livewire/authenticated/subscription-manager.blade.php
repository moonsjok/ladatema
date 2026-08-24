<div>
    <!-- Notification Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-3 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Header & Main Action Buttons -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold mb-1 text-dark">
                <i class="bi bi-credit-card-2-front text-primary me-2"></i> Gestion des Souscriptions
            </h2>
            <p class="text-muted small mb-0">Consultez, validez et gérez toutes les souscriptions et la relance des étudiants.</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm fw-semibold" wire:click="openBulkDurationModal">
                <i class="bi bi-clock-history me-1"></i> Durée par défaut
            </button>
            <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm fw-semibold" wire:click="openCreateModal()">
                <i class="bi bi-plus-circle-fill me-1.5"></i> Nouvelle souscription
            </button>
        </div>
    </div>

    <!-- 📊 KPI Cards Section -->
    <div class="row g-3 mb-4">
        <!-- KPI 1 : Total Subscriptions -->
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-primary cursor-pointer"
                 wire:click="filterByStatus('all')">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-uppercase small fw-bold text-muted mb-1">Total Souscriptions</div>
                        <div class="fs-3 fw-extrabold text-dark">{{ $totalSubscriptions }}</div>
                    </div>
                    <div class="rounded-circle bg-primary-subtle text-primary p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-card-checklist fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI 2 : Pending Subscriptions -->
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-warning cursor-pointer"
                 wire:click="filterByStatus('pending')">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-uppercase small fw-bold text-warning mb-1">En attente validation</div>
                        <div class="fs-3 fw-extrabold text-warning">{{ $pendingSubscriptionsCount }}</div>
                    </div>
                    <div class="rounded-circle bg-warning-subtle text-warning p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-hourglass-split fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI 3 : Students With Subscription -->
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-success cursor-pointer"
                 wire:click="setTab('subscriptions')">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-uppercase small fw-bold text-success mb-1">Étudiants souscrits</div>
                        <div class="fs-3 fw-extrabold text-dark">{{ $studentsWithCount }}</div>
                    </div>
                    <div class="rounded-circle bg-success-subtle text-success p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-person-check-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI 4 : Students Without Subscription -->
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-info cursor-pointer"
                 wire:click="setTab('without_subscriptions')">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-uppercase small fw-bold text-info mb-1">Sans souscription</div>
                        <div class="fs-3 fw-extrabold text-dark">{{ $studentsWithoutCount }}</div>
                    </div>
                    <div class="rounded-circle bg-info-subtle text-info p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-person-exclamation fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 📌 Main Tabs Navigation -->
    <ul class="nav nav-pills custom-nav-pills mb-4 bg-white p-1.5 shadow-sm rounded-4 border">
        <li class="nav-item">
            <button class="nav-link rounded-pill px-4 fw-semibold {{ $activeTab === 'subscriptions' ? 'active' : '' }}"
                    wire:click="setTab('subscriptions')">
                <i class="bi bi-list-task me-1.5"></i> Toutes les Souscriptions
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link rounded-pill px-4 fw-semibold {{ $activeTab === 'without_subscriptions' ? 'active' : '' }}"
                    wire:click="setTab('without_subscriptions')">
                <i class="bi bi-people me-1.5"></i> Étudiants sans souscription
                @if($studentsWithoutCount > 0)
                    <span class="badge bg-danger rounded-pill ms-1.5">{{ $studentsWithoutCount }}</span>
                @endif
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link rounded-pill px-4 fw-semibold {{ $activeTab === 'stats_formations' ? 'active' : '' }}"
                    wire:click="setTab('stats_formations')">
                <i class="bi bi-pie-chart-fill me-1.5"></i> Répartition par formation
            </button>
        </li>
    </ul>

    <!-- 🗂️ TAB 1 : TOUTES LES SOUSCRIPTIONS -->
    @if ($activeTab === 'subscriptions')
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <!-- Filter Bar -->
            <div class="card-header bg-light border-bottom p-3">
                <div class="row g-2 align-items-center">
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" class="form-control border-start-0 ps-0" placeholder="Rechercher étudiant, e-mail, référence..."
                                   wire:model.live.debounce.300ms="search">
                        </div>
                    </div>

                    <div class="col-md-2.5 col-6">
                        <select class="form-select form-select-sm" wire:model.live="statusFilter">
                            <option value="all">Tous les statuts</option>
                            <option value="validated">Validées (Actives)</option>
                            <option value="pending">En attente de validation</option>
                            <option value="expired">Expirées</option>
                            <option value="trashed">Annulées / Supprimées</option>
                        </select>
                    </div>

                    <div class="col-md-2.5 col-6">
                        <select class="form-select form-select-sm" wire:model.live="typeFilter">
                            <option value="all">Tous les types</option>
                            <option value="formation">Formations</option>
                            <option value="course">Cours</option>
                            <option value="chapter">Chapitres</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <select class="form-select form-select-sm" wire:model.live="formationFilter">
                            <option value="">Toutes les formations</option>
                            @foreach ($formationsList as $f)
                                <option value="{{ $f->id }}">{{ $f->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light border-bottom text-muted small text-uppercase fw-bold">
                            <tr>
                                <th class="ps-3">Réf / ID</th>
                                <th>Étudiant</th>
                                <th>Type & Contenu</th>
                                <th>Prix</th>
                                <th>Durée & Expiration</th>
                                <th>Statut</th>
                                <th class="text-end pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subscriptions as $sub)
                                <tr class="{{ $sub->trashed() ? 'table-secondary opacity-75' : '' }}">
                                    <td class="ps-3 fw-bold small text-secondary">
                                        #{{ $sub->id }}
                                        @if($sub->payment_reference)
                                            <div class="badge bg-light text-dark border font-monospace mt-1 d-block text-truncate" style="max-width: 120px;">
                                                {{ $sub->payment_reference }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($sub->user)
                                            <div class="fw-semibold text-dark">{{ $sub->user->name }}</div>
                                            <div class="small text-muted"><i class="bi bi-envelope me-1"></i>{{ $sub->user->email }}</div>
                                        @else
                                            <span class="text-muted italic">Utilisateur supprimé</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary text-uppercase px-2 py-1 small fw-bold mb-1 d-inline-block">
                                            {{ $sub->type }}
                                        </span>
                                        <div class="fw-semibold text-dark small">
                                            @if($sub->formation)
                                                {{ $sub->formation->title }}
                                            @elseif($sub->course)
                                                {{ $sub->course->title }}
                                            @elseif($sub->chapter)
                                                {{ $sub->chapter->title }}
                                            @else
                                                <span class="text-muted">Élément non spécifié</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="fw-bold text-dark">
                                        {{ number_format($sub->price, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="small">
                                        <div><i class="bi bi-calendar-event me-1 text-muted"></i> {{ $sub->duration_in_days }} jours</div>
                                        @if($sub->expires_at)
                                            <div class="text-muted opacity-75 mt-0.5">
                                                Exp : {{ $sub->expires_at->format('d/m/Y') }}
                                                @if(!$sub->trashed() && $sub->is_validated)
                                                    <span class="badge bg-light text-dark border ms-1">{{ $sub->days_remaining }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($sub->trashed())
                                            <span class="badge bg-danger-subtle text-danger border border-danger rounded-pill px-2.5 py-1">
                                                <i class="bi bi-trash-fill me-1"></i> Annulée
                                            </span>
                                        @elseif(!$sub->is_validated)
                                            <span class="badge bg-warning-subtle text-warning border border-warning rounded-pill px-2.5 py-1">
                                                <i class="bi bi-hourglass-split me-1"></i> En attente
                                            </span>
                                        @elseif($sub->isExpired())
                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary rounded-pill px-2.5 py-1">
                                                <i class="bi bi-clock-history me-1"></i> Expirée
                                            </span>
                                        @else
                                            <span class="badge bg-success-subtle text-success border border-success rounded-pill px-2.5 py-1">
                                                <i class="bi bi-check-circle-fill me-1"></i> Validée
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        <div class="btn-group btn-group-sm">
                                            @if(!$sub->trashed() && !$sub->is_validated)
                                                <button class="btn btn-success fw-semibold" title="Valider immédiatement"
                                                        wire:click="validateSubscription({{ $sub->id }})">
                                                    <i class="bi bi-check-lg me-1"></i> Valider
                                                </button>
                                            @endif

                                            @if(!$sub->trashed())
                                                <button class="btn btn-outline-primary" title="Prolonger durée"
                                                        wire:click="openExtendModal({{ $sub->id }})">
                                                    <i class="bi bi-clock"></i>
                                                </button>
                                                <button class="btn btn-outline-secondary" title="Modifier"
                                                        wire:click="openEditModal({{ $sub->id }})">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                                <button class="btn btn-outline-danger" title="Annuler / Supprimer"
                                                        wire:confirm="Êtes-vous sûr de vouloir annuler cette souscription ?"
                                                        wire:click="deleteSubscription({{ $sub->id }})">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            @else
                                                <button class="btn btn-outline-success" title="Restaurer"
                                                        wire:click="restoreSubscription({{ $sub->id }})">
                                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Restaurer
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                        Aucune souscription trouvée pour ces critères.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($subscriptions->hasPages())
                <div class="card-footer bg-light p-3">
                    {{ $subscriptions->links() }}
                </div>
            @endif
        </div>
    @endif

    <!-- 🗂️ TAB 2 : ÉTUDIANTS SANS SOUSCRIPTION -->
    @if ($activeTab === 'without_subscriptions')
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-light border-bottom p-3">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" class="form-control border-start-0 ps-0" placeholder="Rechercher par nom, email, téléphone..."
                                   wire:model.live.debounce.300ms="search">
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end text-muted small mt-2 mt-md-0">
                        Total sans souscription : <strong>{{ $studentsWithoutCount }}</strong> étudiant(s)
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light border-bottom text-muted small text-uppercase fw-bold">
                            <tr>
                                <th class="ps-3">Nom Étudiant</th>
                                <th>Contacts & Réseaux</th>
                                <th>Inscrit le</th>
                                <th class="text-end pe-3">Actions Directes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($studentsWithout as $student)
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-bold text-dark fs-6">{{ $student->name }}</div>
                                        @if($student->prenoms || $student->nom)
                                            <div class="small text-muted">{{ trim(($student->prenoms ?? '') . ' ' . ($student->nom ?? '')) }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="small">
                                            @if($student->email)
                                                <div class="mb-1">
                                                    <i class="bi bi-envelope-fill text-primary me-1.5"></i>
                                                    <a href="mailto:{{ $student->email }}" class="text-decoration-none text-dark">{{ $student->email }}</a>
                                                </div>
                                            @endif
                                            @if($student->phone_call)
                                                <div class="mb-1">
                                                    <i class="bi bi-telephone-fill text-primary me-1.5"></i>
                                                    <a href="tel:{{ preg_replace('/\s+/', '', $student->phone_call) }}" class="text-decoration-none text-dark">{{ $student->phone_call }}</a>
                                                </div>
                                            @endif
                                            @if($student->phone_whatsapp)
                                                <div>
                                                    <i class="bi bi-whatsapp text-success me-1.5"></i>
                                                    <a href="https://wa.me/{{ preg_replace('/\D+/', '', $student->phone_whatsapp) }}" target="_blank" rel="noopener" class="text-decoration-none text-success font-semibold">{{ $student->phone_whatsapp }}</a>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="small text-muted">
                                        {{ $student->created_at ? $student->created_at->format('d/m/Y à H:i') : 'N/A' }}
                                    </td>
                                    <td class="text-end pe-3">
                                        <div class="d-flex justify-content-end gap-2">
                                            <button class="btn btn-sm btn-primary rounded-pill px-3 shadow-xs"
                                                    wire:click="openCreateModal({{ $student->id }})">
                                                <i class="bi bi-plus-circle me-1"></i> Attribuer souscription
                                            </button>
                                            <button class="btn btn-sm btn-outline-warning text-dark rounded-pill px-3 shadow-xs"
                                                    wire:click="openReminderModal({{ $student->id }})">
                                                <i class="bi bi-send-fill me-1 text-warning"></i> Envoyer relance
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="bi bi-check-circle fs-1 text-success d-block mb-2"></i>
                                        Tous les étudiants inscrits possèdent au moins une souscription !
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($studentsWithout->hasPages())
                <div class="card-footer bg-light p-3">
                    {{ $studentsWithout->links() }}
                </div>
            @endif
        </div>
    @endif

    <!-- 🗂️ TAB 3 : REPARTITION PAR FORMATION -->
    @if ($activeTab === 'stats_formations')
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-light border-bottom p-3">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-bar-chart-line-fill text-primary me-2"></i> Statistiques par Formation</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-uppercase small text-muted fw-bold">
                            <tr>
                                <th class="ps-3">Formation</th>
                                <th class="text-center">Total Souscriptions</th>
                                <th class="text-center">Étudiants Uniques</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($statsFormations as $stat)
                                <tr>
                                    <td class="ps-3 fw-bold text-dark fs-6">
                                        <i class="bi bi-book-fill text-primary me-2"></i>
                                        {{ $stat->formation_title }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary rounded-pill px-3 py-1.5 fs-6">
                                            {{ $stat->total_subscriptions }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success-subtle text-success border border-success rounded-pill px-3 py-1.5 fs-6">
                                            {{ $stat->unique_users }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5 text-muted">
                                        Aucune donnée statistique disponible.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- ------------------------------------------------------------- -->
    <!-- 🪟 MODALS LIVEWIRE -->
    <!-- ------------------------------------------------------------- -->

    <!-- MODAL 1: CREATE SUBSCRIPTION -->
    @if($showCreateModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header bg-primary text-white py-3 rounded-top-4">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-plus-circle me-2"></i> Nouvelle Souscription
                        </h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeCreateModal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <form wire:submit.prevent="saveSubscription">
                            <div class="row g-3">
                                <!-- Student Selection -->
                                <div class="col-12">
                                    <label class="form-label fw-bold small">Sélectionner l'étudiant <span class="text-danger">*</span></label>
                                    <select class="form-select @error('create_user_id') is-invalid @enderror" wire:model.live="create_user_id">
                                        <option value="">-- Choisir un étudiant --</option>
                                        @foreach($studentsList as $st)
                                            <option value="{{ $st->id }}">{{ $st->name }} ({{ $st->email }})</option>
                                        @endforeach
                                    </select>
                                    @error('create_user_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <!-- Type -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">Type d'élément <span class="text-danger">*</span></label>
                                    <select class="form-select @error('create_type') is-invalid @enderror" wire:model.live="create_type">
                                        <option value="formation">Formation</option>
                                        <option value="course">Cours</option>
                                        <option value="chapter">Chapitre</option>
                                    </select>
                                    @error('create_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <!-- Item ID -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">Élément pédagogique <span class="text-danger">*</span></label>
                                    <select class="form-select @error('create_typeid') is-invalid @enderror" wire:model.live="create_typeid">
                                        <option value="">-- Sélectionner --</option>
                                        @if($create_type === 'formation')
                                            @foreach($formationsList as $f)
                                                <option value="{{ $f->id }}">{{ $f->title }}</option>
                                            @endforeach
                                        @elseif($create_type === 'course')
                                            @foreach($coursesList as $c)
                                                <option value="{{ $c->id }}">{{ $c->title }}</option>
                                            @endforeach
                                        @elseif($create_type === 'chapter')
                                            @foreach($chaptersList as $ch)
                                                <option value="{{ $ch->id }}">{{ $ch->title }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('create_typeid') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <!-- Price -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">Prix (FCFA) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('create_price') is-invalid @enderror" wire:model.live="create_price" min="0">
                                    @error('create_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <!-- Duration -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">Durée de validité (Jours) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('create_duration_in_days') is-invalid @enderror" wire:model.live="create_duration_in_days" min="1" max="365">
                                    <div class="form-text">Minimum conseillé : 90 jours (3 mois).</div>
                                    @error('create_duration_in_days') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <!-- Payment reference -->
                                <div class="col-md-8">
                                    <label class="form-label fw-bold small">Référence de paiement (Optionnel)</label>
                                    <input type="text" class="form-control @error('create_payment_reference') is-invalid @enderror" wire:model.live="create_payment_reference" placeholder="Ex: REF-FEDAPAY-12345">
                                    @error('create_payment_reference') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <!-- Is Validated Checkbox -->
                                <div class="col-md-4 d-flex align-items-center mt-4">
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" id="createValidated" wire:model.live="create_is_validated">
                                        <label class="form-check-label fw-bold small text-dark" for="createValidated">
                                            Valider directement l'accès
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer border-top-0 px-0 pb-0 mt-4">
                                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" wire:click="closeCreateModal">Annuler</button>
                                <button type="submit" class="btn btn-primary rounded-pill px-4">
                                    <span wire:loading.remove wire:target="saveSubscription"><i class="bi bi-check-lg me-1"></i> Créer la souscription</span>
                                    <span wire:loading wire:target="saveSubscription"><span class="spinner-border spinner-border-sm me-1"></span> Enregistrement...</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL 2: EDIT SUBSCRIPTION -->
    @if($showEditModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header bg-secondary text-white py-3 rounded-top-4">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-pencil-square me-2"></i> Modifier la Souscription #{{ $editingSubscriptionId }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeEditModal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <form wire:submit.prevent="updateSubscription">
                            <div class="row g-3">
                                <!-- Student Selection -->
                                <div class="col-12">
                                    <label class="form-label fw-bold small">Étudiant</label>
                                    <select class="form-select @error('edit_user_id') is-invalid @enderror" wire:model.live="edit_user_id">
                                        @foreach($studentsList as $st)
                                            <option value="{{ $st->id }}">{{ $st->name }} ({{ $st->email }})</option>
                                        @endforeach
                                    </select>
                                    @error('edit_user_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <!-- Type -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">Type d'élément</label>
                                    <select class="form-select @error('edit_type') is-invalid @enderror" wire:model.live="edit_type">
                                        <option value="formation">Formation</option>
                                        <option value="course">Cours</option>
                                        <option value="chapter">Chapitre</option>
                                    </select>
                                    @error('edit_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <!-- Item ID -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">Élément pédagogique</label>
                                    <select class="form-select @error('edit_typeid') is-invalid @enderror" wire:model.live="edit_typeid">
                                        @if($edit_type === 'formation')
                                            @foreach($formationsList as $f)
                                                <option value="{{ $f->id }}">{{ $f->title }}</option>
                                            @endforeach
                                        @elseif($edit_type === 'course')
                                            @foreach($coursesList as $c)
                                                <option value="{{ $c->id }}">{{ $c->title }}</option>
                                            @endforeach
                                        @elseif($edit_type === 'chapter')
                                            @foreach($chaptersList as $ch)
                                                <option value="{{ $ch->id }}">{{ $ch->title }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('edit_typeid') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <!-- Price -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">Prix (FCFA)</label>
                                    <input type="number" class="form-control @error('edit_price') is-invalid @enderror" wire:model.live="edit_price" min="0">
                                    @error('edit_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <!-- Duration -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">Durée de validité (Jours)</label>
                                    <input type="number" class="form-control @error('edit_duration_in_days') is-invalid @enderror" wire:model.live="edit_duration_in_days" min="1" max="365">
                                    @error('edit_duration_in_days') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <!-- Payment reference -->
                                <div class="col-md-8">
                                    <label class="form-label fw-bold small">Référence de paiement</label>
                                    <input type="text" class="form-control @error('edit_payment_reference') is-invalid @enderror" wire:model.live="edit_payment_reference">
                                    @error('edit_payment_reference') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <!-- Is Validated Checkbox -->
                                <div class="col-md-4 d-flex align-items-center mt-4">
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" id="editValidated" wire:model.live="edit_is_validated">
                                        <label class="form-check-label fw-bold small text-dark" for="editValidated">
                                            Accès Validé
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer border-top-0 px-0 pb-0 mt-4">
                                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" wire:click="closeEditModal">Annuler</button>
                                <button type="submit" class="btn btn-primary rounded-pill px-4">
                                    <span wire:loading.remove wire:target="updateSubscription"><i class="bi bi-save me-1"></i> Enregistrer modifications</span>
                                    <span wire:loading wire:target="updateSubscription"><span class="spinner-border spinner-border-sm me-1"></span> Mise à jour...</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL 3: EXTEND DURATION -->
    @if($showExtendModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header bg-info text-white py-3 rounded-top-4">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-clock-history me-2"></i> Prolonger la Souscription
                        </h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeExtendModal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <form wire:submit.prevent="saveExtendSubscription">
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Jours supplémentaires à ajouter <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('additional_days') is-invalid @enderror" wire:model.live="additional_days" min="1" max="365">
                                <div class="form-text">Exemple : 30 jours, 60 jours, 90 jours...</div>
                                @error('additional_days') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="modal-footer border-top-0 px-0 pb-0 mt-4">
                                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" wire:click="closeExtendModal">Annuler</button>
                                <button type="submit" class="btn btn-info text-white rounded-pill px-4 fw-bold">
                                    <span wire:loading.remove wire:target="saveExtendSubscription"><i class="bi bi-plus-circle me-1"></i> Ajouter les jours</span>
                                    <span wire:loading wire:target="saveExtendSubscription"><span class="spinner-border spinner-border-sm me-1"></span> Traitement...</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL 4: REMINDER EMAIL -->
    @if($showReminderModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header bg-warning text-dark py-3 rounded-top-4">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-envelope-at me-2"></i> Relance par e-mail : {{ $reminderUserName }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeReminderModal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <form wire:submit.prevent="sendReminderEmail">
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Destinataire</label>
                                <input type="text" class="form-control" value="{{ $reminderUserName }} ({{ $reminderUserEmail }})" disabled>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small">Message de relance <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('reminderMessage') is-invalid @enderror" rows="6" wire:model.live="reminderMessage"></textarea>
                                @error('reminderMessage') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="modal-footer border-top-0 px-0 pb-0 mt-4">
                                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" wire:click="closeReminderModal">Annuler</button>
                                <button type="submit" class="btn btn-warning text-dark rounded-pill px-4 fw-bold">
                                    <span wire:loading.remove wire:target="sendReminderEmail"><i class="bi bi-send-fill me-1"></i> Envoyer le mail</span>
                                    <span wire:loading wire:target="sendReminderEmail"><span class="spinner-border spinner-border-sm me-1"></span> Envoi en cours...</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL 5: BULK DURATION -->
    @if($showBulkDurationModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header bg-dark text-white py-3 rounded-top-4">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-sliders me-2"></i> Définir Durée par Défaut Groupée
                        </h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeBulkDurationModal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <form wire:submit.prevent="saveBulkDuration">
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Durée en jours (Minimum 90 jours) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('bulk_duration_in_days') is-invalid @enderror" wire:model.live="bulk_duration_in_days" min="90" max="365">
                                @error('bulk_duration_in_days') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="onlyWithoutExp" wire:model.live="only_without_expiration">
                                <label class="form-check-label small fw-semibold" for="onlyWithoutExp">
                                    Ne mettre à jour que les souscriptions sans date d'expiration
                                </label>
                            </div>

                            <div class="modal-footer border-top-0 px-0 pb-0 mt-4">
                                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" wire:click="closeBulkDurationModal">Annuler</button>
                                <button type="submit" class="btn btn-dark rounded-pill px-4">
                                    <span wire:loading.remove wire:target="saveBulkDuration"><i class="bi bi-check-lg me-1"></i> Appliquer à toutes</span>
                                    <span wire:loading wire:target="saveBulkDuration"><span class="spinner-border spinner-border-sm me-1"></span> Application...</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
