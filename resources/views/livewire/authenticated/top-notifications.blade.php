<div id="top-notifications-root" wire:poll.15s class="mb-3">
    @if ($notifications && $notifications->count() > 0)
        <div class="google-notifications-container">
            <!-- Barre de titre globale si plusieurs notifications -->
            @if($notifications->count() > 1)
                <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                    <div class="d-flex align-items-center">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 fs-7 me-2">
                            <i class="bi bi-bell-fill me-1"></i> {{ $notifications->count() }} NOUVELLES NOTIFICATIONS
                        </span>
                    </div>
                    <button type="button" 
                            class="btn btn-link btn-sm text-decoration-none text-muted p-0 border-0 fw-semibold" 
                            wire:click="markAllAsRead" 
                            wire:loading.attr="disabled"
                            style="font-size: 0.82rem;">
                        <i class="bi bi-check-all me-1 text-primary fs-6"></i> Tout marquer comme lu
                    </button>
                </div>
            @endif

            @foreach ($notifications as $notif)
                <div class="google-notif-card card border-0 shadow-sm rounded-3 mb-2.5 overflow-hidden border-start border-4 border-{{ $notif->is_important ? 'danger' : 'primary' }}"
                     style="background: {{ $notif->is_important ? '#fff8f8' : '#f8fafc' }}; transition: all 0.2s ease-in-out;"
                     wire:key="top-notif-{{ $notif->id }}">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-start">
                            <!-- Badge d'icône style Google Material -->
                            <div class="me-3 flex-shrink-0">
                                <div class="rounded-circle d-flex align-items-center justify-content-center shadow-xs" 
                                     style="width: 42px; height: 42px; background: {{ $notif->is_important ? '#fee2e2' : '#e8f0fe' }}; color: {{ $notif->is_important ? '#dc2626' : '#1a73e8' }};">
                                    <i class="bi {{ $notif->is_important ? 'bi-shield-exclamation' : 'bi-megaphone-fill' }} fs-5"></i>
                                </div>
                            </div>

                            <!-- Contenu principal -->
                            <div class="flex-grow-1 me-2">
                                <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
                                    <span class="badge bg-{{ $notif->is_important ? 'danger' : 'primary' }} px-2 py-0.5 rounded-pill" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                                        {{ $notif->is_important ? 'URGENT' : 'ANNONCE' }}
                                    </span>
                                    <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.98rem; font-family: system-ui, -apple-system, sans-serif;">
                                        {!! $notif->title !!}
                                    </h6>
                                </div>

                                <div class="text-secondary mb-2" style="font-size: 0.9rem; line-height: 1.55; white-space: pre-line; color: #374151;">
                                    {!! $notif->message !!}
                                </div>

                                <div class="d-flex align-items-center text-muted small" style="font-size: 0.78rem;">
                                    <i class="bi bi-clock me-1 text-primary"></i>
                                    <span>{{ $notif->created_at->diffForHumans() }}</span>
                                    <span class="mx-1.5">•</span>
                                    <i class="bi bi-person-circle me-1"></i>
                                    <span>De : {{ $notif->sender->name ?? 'Administration' }}</span>
                                </div>
                            </div>

                            <!-- Bouton d'action -->
                            <div class="flex-shrink-0 ms-2">
                                <button type="button" 
                                        class="btn btn-sm btn-white border shadow-xs text-secondary rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center"
                                        style="font-size: 0.82rem; background: #ffffff;"
                                        wire:click="markAsRead({{ $notif->id }})"
                                        wire:loading.attr="disabled"
                                        title="Marquer comme lu">
                                    <span wire:loading.remove wire:target="markAsRead({{ $notif->id }})">
                                        <i class="bi bi-check2 text-success me-1 fs-6"></i> Marquer comme lu
                                    </span>
                                    <span wire:loading wire:target="markAsRead({{ $notif->id }})">
                                        <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
