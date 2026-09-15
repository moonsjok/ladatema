<div id="user-manager-root">
    <!-- Style spécifique pour l'impression PDF / Papier (Imprimer uniquement le tableau) -->
    <style>
        .print-header {
            display: none;
        }

        @media print {
            /* Masquer la navigation, les barres latérales, les cartes KPI, les filtres, la recherche et la pagination */
            nav, sidebar, .sidebar, .offcanvas, .navbar, .header, header,
            .no-print, .btn, .dropdown-menu, .modal, .pagination,
            #whatsapp-contact-widget, footer, .kpi-cards-container {
                display: none !important;
            }

            body, #user-manager-root, .container-fluid, .card, .table-responsive {
                background: #ffffff !important;
                color: #000000 !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                box-shadow: none !important;
                border: none !important;
            }

            /* Affichage de l'en-tête d'impression */
            .print-header {
                display: block !important;
                text-align: center;
                margin-bottom: 20px;
                padding-bottom: 10px;
                border-bottom: 2px solid #333;
            }

            /* Masquer la colonne Actions */
            th:last-child, td:last-child {
                display: none !important;
            }

            table {
                width: 100% !important;
                border-collapse: collapse !important;
            }

            th, td {
                border: 1px solid #ccc !important;
                padding: 8px 10px !important;
                font-size: 11px !important;
            }

            thead {
                background-color: #f5f5f5 !important;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>

    <!-- En-tête imprimable (visible uniquement à l'impression) -->
    <div class="print-header">
        <h3 style="margin: 0; font-weight: bold; color: #0E2E72;">LADATEMA GROUP — Liste des Utilisateurs</h3>
        <p style="margin: 4px 0 0 0; font-size: 12px; color: #666;">Document généré le {{ date('d/m/Y à H:i') }}</p>
    </div>

    <!-- En-tête / Cartes KPI -->
    <div class="row g-3 mb-4 no-print kpi-cards-container">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-primary text-white p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-white-50 small fw-semibold text-uppercase">Total Utilisateurs</div>
                        <h3 class="fw-bold mb-0 text-white mt-1">{{ $totalUsersCount }}</h3>
                    </div>
                    <div class="bg-white-20 rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: rgba(255,255,255,0.2);">
                        <i class="bi bi-people-fill fs-4 text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-success text-white p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-white-50 small fw-semibold text-uppercase">Comptes Actifs</div>
                        <h3 class="fw-bold mb-0 text-white mt-1">{{ $activeUsersCount }}</h3>
                    </div>
                    <div class="bg-white-20 rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: rgba(255,255,255,0.2);">
                        <i class="bi bi-check-circle-fill fs-4 text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-danger text-white p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-white-50 small fw-semibold text-uppercase">Comptes Désactivés</div>
                        <h3 class="fw-bold mb-0 text-white mt-1">{{ $disabledUsersCount }}</h3>
                    </div>
                    <div class="bg-white-20 rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: rgba(255,255,255,0.2);">
                        <i class="bi bi-person-x-fill fs-4 text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-dark text-white p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-white-50 small fw-semibold text-uppercase">Total Apprenants</div>
                        <h3 class="fw-bold mb-0 text-white mt-1">{{ $totalStudentsCount }}</h3>
                    </div>
                    <div class="bg-white-20 rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: rgba(255,255,255,0.2);">
                        <i class="bi bi-mortarboard-fill fs-4 text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Barre d'actions & Filtres (Organisée sur 2 Lignes) -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 no-print">
        <div class="card-body p-3">
            <!-- LIGNE 1 : Onglets de filtrage par Rôle / Statut -->
            <div class="nav nav-pills gap-1 flex-wrap mb-3 pb-3 border-bottom">
                <button class="nav-link rounded-pill px-3 py-1.5 small fw-semibold {{ $activeTab === 'all' ? 'active' : '' }}" wire:click="setTab('all')">
                    Tous ({{ $totalUsersCount }})
                </button>
                <button class="nav-link rounded-pill px-3 py-1.5 small fw-semibold {{ $activeTab === 'student' ? 'active' : '' }}" wire:click="setTab('student')">
                    Étudiants
                </button>
                <button class="nav-link rounded-pill px-3 py-1.5 small fw-semibold {{ $activeTab === 'employee' ? 'active' : '' }}" wire:click="setTab('employee')">
                    Employés
                </button>
                <button class="nav-link rounded-pill px-3 py-1.5 small fw-semibold {{ $activeTab === 'admin' ? 'active' : '' }}" wire:click="setTab('admin')">
                    {{ $isDev ? 'Admins & Devs' : 'Administrateurs' }}
                </button>
                <button class="nav-link rounded-pill px-3 py-1.5 small fw-semibold text-danger {{ $activeTab === 'disabled' ? 'active bg-danger text-white' : '' }}" wire:click="setTab('disabled')">
                    <i class="bi bi-person-x-fill me-1"></i> Désactivés ({{ $disabledUsersCount }})
                </button>
                <button class="nav-link rounded-pill px-3 py-1.5 small fw-semibold text-secondary {{ $activeTab === 'trashed' ? 'active bg-secondary text-white' : '' }}" wire:click="setTab('trashed')">
                    <i class="bi bi-trash me-1"></i> Corbeille
                </button>
            </div>

            <!-- LIGNE 2 : Boutons d'exportation (Gauche) & Recherche (Droite) -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <button class="btn btn-outline-success btn-sm rounded-pill px-3 fw-semibold d-inline-flex align-items-center" wire:click="exportCsv">
                        <i class="bi bi-file-earmark-excel me-1.5"></i> Exporter (CSV/Excel)
                    </button>
                    <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-semibold d-inline-flex align-items-center" onclick="window.print()">
                        <i class="bi bi-printer me-1.5"></i> Imprimer
                    </button>
                </div>

                <!-- Champ de Recherche -->
                <div class="position-relative ms-md-auto" style="min-width: 260px; max-width: 380px; width: 100%;">
                    <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                    <input type="text" class="form-control form-control-sm rounded-pill ps-5 pe-3" 
                           placeholder="Rechercher nom, email, téléphone..." 
                           wire:model.live.debounce.300ms="search">
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau de la Liste des Utilisateurs -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Utilisateur</th>
                            <th>Rôle</th>
                            <th>Coordonnées</th>
                            <th>Statut du Compte</th>
                            <th>Inscrit le</th>
                            <th class="text-end pe-4 no-print">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $userItem)
                            <tr class="{{ $userItem->trashed() ? 'table-secondary opacity-75' : '' }}">
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        @if ($userItem->hasMedia('avatars'))
                                            <img src="{{ $userItem->getFirstMediaUrl('avatars') }}" class="rounded-circle border" style="width: 42px; height: 42px; object-fit: cover;" alt="Avatar">
                                        @else
                                            <div class="rounded-circle bg-primary text-white fw-bold d-flex align-items-center justify-content-center shadow-xs" style="width: 42px; height: 42px; font-size: 1.1rem;">
                                                {{ strtoupper(substr($userItem->name ?? 'U', 0, 1)) }}
                                            </div>
                                        @endif

                                        <div>
                                            <div class="fw-bold text-dark mb-0">{{ $userItem->name }}</div>
                                            <div class="text-muted small">{{ $userItem->email }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    @switch($userItem->role)
                                        @case('dev')
                                            @if($isDev)
                                                <span class="badge bg-danger rounded-pill px-3 py-1.5 fw-semibold"><i class="bi bi-code-slash me-1"></i> Dev / SuperAdmin</span>
                                            @else
                                                <span class="badge bg-primary rounded-pill px-3 py-1.5 fw-semibold"><i class="bi bi-shield-lock-fill me-1"></i> Administrateur</span>
                                            @endif
                                            @break
                                        @case('owner')
                                            <span class="badge bg-primary rounded-pill px-3 py-1.5 fw-semibold"><i class="bi bi-shield-lock-fill me-1"></i> Administrateur</span>
                                            @break
                                        @case('employee')
                                            <span class="badge bg-info text-dark rounded-pill px-3 py-1.5 fw-semibold"><i class="bi bi-person-badge me-1"></i> Employé</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary rounded-pill px-3 py-1.5 fw-semibold"><i class="bi bi-mortarboard me-1"></i> Étudiant</span>
                                    @endswitch
                                </td>

                                <td>
                                    @if (!empty($userItem->phone_call))
                                        <div>
                                            <a href="tel:{{ $userItem->phone_call }}" class="text-decoration-none text-dark small fw-semibold">
                                                <i class="bi bi-telephone text-primary me-1"></i>{{ $userItem->phone_call }}
                                            </a>
                                        </div>
                                    @endif
                                    @if (!empty($userItem->phone_whatsapp))
                                        <div>
                                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $userItem->phone_whatsapp) }}" target="_blank" class="text-decoration-none text-success small fw-semibold">
                                                <i class="bi bi-whatsapp me-1"></i>WhatsApp
                                            </a>
                                        </div>
                                    @endif
                                    @if (empty($userItem->phone_call) && empty($userItem->phone_whatsapp))
                                        <span class="text-muted small">Aucun contact</span>
                                    @endif
                                </td>

                                <td>
                                    @if ($userItem->trashed())
                                        <span class="badge bg-secondary rounded-pill px-3 py-1.5"><i class="bi bi-archive me-1"></i> Corbeille</span>
                                    @elseif (isset($userItem->is_active) && $userItem->is_active === false)
                                        <span class="badge bg-danger rounded-pill px-3 py-1.5 fw-semibold"><i class="bi bi-person-x-fill me-1"></i> Désactivé</span>
                                    @else
                                        <span class="badge bg-success rounded-pill px-3 py-1.5 fw-semibold"><i class="bi bi-check-circle-fill me-1"></i> Actif</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="small fw-semibold text-dark">{{ $userItem->created_at ? $userItem->created_at->format('d/m/Y') : 'N/A' }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">{{ $userItem->created_at ? $userItem->created_at->diffForHumans() : '' }}</div>
                                </td>

                                <td class="text-end pe-4 no-print">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light rounded-circle shadow-xs" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                                            <!-- Activer / Désactiver -->
                                            @if (!$userItem->trashed() && $userItem->id !== auth()->id())
                                                <li>
                                                    <button class="dropdown-item d-flex align-items-center gap-2 {{ $userItem->is_active ? 'text-danger' : 'text-success' }}" 
                                                            wire:click="toggleUserStatus({{ $userItem->id }})">
                                                        <i class="bi {{ $userItem->is_active ? 'bi-person-x-fill' : 'bi-check-circle-fill' }}"></i>
                                                        {{ $userItem->is_active ? 'Désactiver le compte' : 'Activer le compte' }}
                                                    </button>
                                                </li>
                                            @endif

                                            <!-- Édition complète (Dev) ou Modifier rôle (Owner) -->
                                            @if (!$userItem->trashed())
                                                @if ($isDev)
                                                    <li>
                                                        <button class="dropdown-item d-flex align-items-center gap-2 text-primary fw-semibold" 
                                                                wire:click="editUser({{ $userItem->id }})" 
                                                                data-bs-toggle="modal" data-bs-target="#editFullUserModal">
                                                            <i class="bi bi-pencil-square"></i> Modifier le profil complet
                                                        </button>
                                                    </li>
                                                @else
                                                    <li>
                                                        <button class="dropdown-item d-flex align-items-center gap-2" 
                                                                wire:click="editRole({{ $userItem->id }})" 
                                                                data-bs-toggle="modal" data-bs-target="#editRoleModal">
                                                            <i class="bi bi-shield-gear"></i> Modifier le rôle
                                                        </button>
                                                    </li>
                                                @endif
                                            @endif

                                            @if ($userItem->trashed())
                                                <li>
                                                    <button class="dropdown-item d-flex align-items-center gap-2 text-success" 
                                                            wire:click="restoreUser({{ $userItem->id }})">
                                                        <i class="bi bi-arrow-counterclockwise"></i> Restaurer
                                                    </button>
                                                </li>
                                            @endif

                                            <!-- Suppression sans contrainte (Reservé au Dev) -->
                                            @if ($isDev && $userItem->id !== auth()->id())
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <button class="dropdown-item d-flex align-items-center gap-2 text-danger fw-semibold" 
                                                            wire:click="confirmDelete({{ $userItem->id }})" 
                                                            data-bs-toggle="modal" data-bs-target="#deleteUserModal">
                                                        <i class="bi bi-trash3-fill"></i> Supprimer définitivement
                                                    </button>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-people fs-1 d-block mb-2 text-secondary"></i>
                                    Aucun utilisateur ne correspond à votre recherche.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-3 border-top d-flex justify-content-end no-print">
                {{ $users->links() }}
            </div>
        </div>
    </div>

    <!-- Modal Modification de Rôle (Owner) -->
    <div wire:ignore.self class="modal fade no-print" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header bg-light border-0 py-3">
                    <h5 class="modal-title fw-bold" id="editRoleModalLabel">
                        <i class="bi bi-shield-gear text-primary me-2"></i>Modifier le rôle de l'utilisateur
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Choisir le nouveau rôle :</label>
                        <select class="form-select rounded-3" wire:model="editingUserRole">
                            <option value="student">Étudiant (Apprenant)</option>
                            <option value="employee">Employé</option>
                            <option value="owner">Administrateur</option>
                            @if($isDev)
                                <option value="dev">Développeur (Dev / SuperAdmin)</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-primary rounded-pill px-4 fw-semibold" wire:click="saveRole" data-bs-dismiss="modal">
                        Enregistrer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Édition Profil Complet (Dev Only) -->
    <div wire:ignore.self class="modal fade no-print" id="editFullUserModal" tabindex="-1" aria-labelledby="editFullUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header bg-primary text-white border-0 py-3">
                    <h5 class="modal-title fw-bold" id="editFullUserModalLabel">
                        <i class="bi bi-person-gear me-2"></i>Édition Complète du Profil (Dev)
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nom d'affichage (Name) :</label>
                            <input type="text" class="form-control rounded-3" wire:model="editingName">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Adresse e-mail :</label>
                            <input type="email" class="form-control rounded-3" wire:model="editingEmail">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nom de famille :</label>
                            <input type="text" class="form-control rounded-3" wire:model="editingNom">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Prénoms :</label>
                            <input type="text" class="form-control rounded-3" wire:model="editingPrenoms">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Téléphone Appels :</label>
                            <input type="text" class="form-control rounded-3" wire:model="editingPhoneCall">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Téléphone WhatsApp :</label>
                            <input type="text" class="form-control rounded-3" wire:model="editingPhoneWhatsapp">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Rôle du Compte :</label>
                            <select class="form-select rounded-3" wire:model="editingUserRole">
                                <option value="student">Étudiant (Apprenant)</option>
                                <option value="employee">Employé</option>
                                <option value="owner">Administrateur</option>
                                <option value="dev">Développeur (Dev / SuperAdmin)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Statut du compte :</label>
                            <select class="form-select rounded-3" wire:model="editingIsActive">
                                <option value="1">Actif</option>
                                <option value="0">Désactivé</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <hr class="my-2 text-muted">
                            <label class="form-label fw-semibold text-danger">Nouveau Mot de Passe (laisser vide pour ne pas modifier) :</label>
                            <input type="password" class="form-control rounded-3" placeholder="Saisir un nouveau mot de passe si souhaité..." wire:model="editingPassword">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-primary rounded-pill px-4 fw-semibold" wire:click="saveFullUser" data-bs-dismiss="modal">
                        Enregistrer les Modifications
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Confirmation Suppression Définitive (Dev Only) -->
    <div wire:ignore.self class="modal fade no-print" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header bg-danger text-white border-0 py-3">
                    <h5 class="modal-title fw-bold" id="deleteUserModalLabel">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>Confirmation de suppression définitive
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="mb-2">Êtes-vous sûr de vouloir supprimer définitivement l'utilisateur <strong>{{ $confirmingUserName }}</strong> ?</p>
                    <div class="alert alert-warning border-0 rounded-3 small mb-0">
                        <i class="bi bi-info-circle-fill me-1"></i>
                        <strong>Nettoyage automatique sans contraintes :</strong> Tous les éléments liés (profil, souscriptions, examens, sessions et messages) seront supprimés sans aucun blocage SQL. Cette action est irréversible.
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-danger rounded-pill px-4 fw-semibold" wire:click="forceDeleteUser" data-bs-dismiss="modal">
                        Supprimer définitivement
                    </button>
                </div>
            </div>
        </div>
    </div>

@script
    <script>
        $wire.on('swal:alert', data => {
            Swal.fire({
                icon: data[0].icon,
                title: data[0].title,
                text: data[0].text,
                confirmButtonText: 'J\'ai compris'
            });
        });
    </script>
@endscript
</div>
