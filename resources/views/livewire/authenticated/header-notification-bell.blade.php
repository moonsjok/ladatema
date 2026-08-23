<div id="header-notification-bell-root" class="dropdown d-inline-block me-3" wire:poll.15s>
    <a class="nav-item nav-link position-relative d-inline-flex align-items-center text-dark text-decoration-none p-1" 
       href="#" 
       id="headerNotificationDropdown" 
       role="button" 
       data-bs-toggle="dropdown" 
       aria-expanded="false" 
       title="Notifications">
        <i class="bi bi-bell-fill fs-5 text-secondary"></i>
        @if($unreadCount > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light" style="font-size: 0.65rem; padding: 0.3em 0.5em;">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                <span class="visually-hidden">Notifications non lues</span>
            </span>
        @endif
    </a>

    <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-0 mt-2" 
         aria-labelledby="headerNotificationDropdown" 
         style="width: 350px; max-width: 90vw;">
        
        <!-- En-tête Dropdown -->
        <div class="p-3 bg-light rounded-top-4 border-bottom d-flex justify-content-between align-items-center">
            <div class="fw-bold text-dark fs-6">
                <i class="bi bi-bell me-1.5 text-primary"></i> Notifications
                @if($unreadCount > 0)
                    <span class="badge bg-danger rounded-pill ms-1" style="font-size: 0.7rem;">{{ $unreadCount }} non lue(s)</span>
                @endif
            </div>
            @if($unreadCount > 0)
                <button type="button" class="btn btn-link btn-sm text-primary p-0 text-decoration-none small fw-semibold" wire:click="markAllAsRead">
                    <i class="bi bi-check-all me-1"></i> Tout marquer lu
                </button>
            @endif
        </div>

        <!-- Liste des notifications -->
        <div class="list-group list-group-flush overflow-y-auto" style="max-height: 420px;">
            @if($notifications->isEmpty())
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-inbox fs-3 d-block mb-1 text-secondary"></i>
                    <p class="mb-0 small">Aucune notification reçue pour le moment.</p>
                </div>
            @else
                @foreach($notifications as $notif)
                    <div class="list-group-item p-3 border-bottom {{ $notif->is_read ? 'bg-white' : 'bg-primary-subtle border-start border-4 border-primary' }}">
                        <!-- Ligne 1 : Icône sur sa propre ligne -->
                        <div class="mb-1.5">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center shadow-xs" 
                                 style="width: 34px; height: 34px; background: {{ $notif->is_important ? '#fee2e2' : '#e8f0fe' }}; color: {{ $notif->is_important ? '#dc2626' : '#1a73e8' }};">
                                <i class="bi {{ $notif->is_important ? 'bi-exclamation-circle-fill' : 'bi-bell-fill' }} fs-6"></i>
                            </div>
                        </div>

                        <!-- Ligne 2 : Importance sur la seconde ligne -->
                        <div class="mb-1.5">
                            <span class="badge bg-{{ $notif->is_important ? 'danger' : 'primary' }} px-2 py-0.5 rounded-pill" style="font-size: 0.68rem; letter-spacing: 0.5px;">
                                {{ $notif->is_important ? 'IMPORTANT' : 'ANNONCE' }}
                            </span>
                            @if($notif->is_read)
                                <span class="badge bg-light text-secondary border rounded-pill px-2 py-0.5 ms-1" style="font-size: 0.65rem;">
                                    <i class="bi bi-check2-all text-success me-0.5"></i> Déjà lu
                                </span>
                            @endif
                        </div>

                        <!-- Ligne 3 : Titre sur la troisième ligne -->
                        <div class="mb-1.5">
                            <div class="fw-bold text-dark small" style="font-size: 0.9rem;">
                                {!! $notif->title !!}
                            </div>
                        </div>

                        <!-- Ligne 4 : Message Justifié -->
                        <div class="small text-secondary mb-2" style="font-size: 0.82rem; line-height: 1.5; white-space: pre-line; text-align: justify; color: #374151;">
                            {!! $notif->message !!}
                        </div>

                        <!-- Ligne 5 : Date et heure de publication -->
                        <div class="small text-muted mb-1" style="font-size: 0.75rem;">
                            <i class="bi bi-calendar-event me-1 text-primary"></i>
                            <strong>Date & heure :</strong> {{ $notif->created_at->format('d/m/Y à H:i') }}
                        </div>

                        <!-- Ligne 6 : Temps écoulé -->
                        <div class="small text-muted mb-1" style="font-size: 0.75rem;">
                            <i class="bi bi-clock-history me-1 text-primary"></i>
                            <strong>Temps écoulé :</strong> {{ $notif->created_at->diffForHumans() }}
                        </div>

                        <!-- Ligne 7 : Expéditeur -->
                        <div class="small text-muted mb-2" style="font-size: 0.75rem;">
                            <i class="bi bi-person-circle me-1 text-primary"></i>
                            <strong>Expéditeur :</strong> {{ $notif->sender->name ?? 'Administration' }}
                        </div>

                        <!-- Ligne 8 : Bouton d'action sur une nouvelle ligne -->
                        @if(!$notif->is_read)
                            <div>
                                <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 0.75rem;" wire:click="markAsRead({{ $notif->id }})">
                                    <i class="bi bi-check-lg me-1"></i> Marquer comme lu
                                </button>
                            </div>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>

        <!-- Pied Dropdown avec lien vers la page complète -->
        <div class="p-2 bg-light rounded-bottom-4 border-top text-center">
            <a href="{{ route('user.notifications') }}" class="btn btn-sm btn-link text-primary text-decoration-none fw-bold small">
                <i class="bi bi-folder2-open me-1"></i> Voir toutes mes notifications <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
</div>
