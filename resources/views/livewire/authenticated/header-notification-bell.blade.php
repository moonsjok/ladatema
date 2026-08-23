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
        <div class="list-group list-group-flush overflow-y-auto" style="max-height: 360px;">
            @if($notifications->isEmpty())
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-inbox fs-3 d-block mb-1 text-secondary"></i>
                    <p class="mb-0 small">Aucune notification reçue pour le moment.</p>
                </div>
            @else
                @foreach($notifications as $notif)
                    <div class="list-group-item p-3 border-bottom {{ $notif->is_read ? 'bg-white' : 'bg-primary-subtle border-start border-3 border-primary' }}">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <div class="fw-bold text-dark small text-truncate pe-2" style="max-width: 220px;">
                                @if($notif->is_important)
                                    <span class="badge bg-danger me-1" style="font-size: 0.65rem;">Important</span>
                                @endif
                                {!! $notif->title !!}
                            </div>
                            <span class="text-muted opacity-75" style="font-size: 0.72rem;">
                                {{ $notif->created_at->diffForHumans() }}
                            </span>
                        </div>

                        <p class="small text-secondary mb-2 text-break" style="font-size: 0.82rem; line-height: 1.4; white-space: pre-line;">
                            {!! $notif->message !!}
                        </p>

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted" style="font-size: 0.72rem;">
                                <i class="bi bi-person me-1"></i> {{ $notif->sender->name ?? 'Admin' }}
                            </span>

                            @if(!$notif->is_read)
                                <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2 py-0.5" style="font-size: 0.72rem;" wire:click="markAsRead({{ $notif->id }})">
                                    <i class="bi bi-check-lg me-0.5"></i> Lu
                                </button>
                            @else
                                <span class="badge bg-light text-secondary border px-2 py-0.5" style="font-size: 0.7rem;">
                                    <i class="bi bi-check2-all text-success me-0.5"></i> Déjà lu
                                </span>
                            @endif
                        </div>
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
