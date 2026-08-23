<div id="top-notifications-root" wire:poll.15s class="mb-3">
    @if ($notifications && $notifications->count() > 0)
        <div class="google-notifications-container">
            <!-- Barre de titre globale si plusieurs notifications -->
            @if($notifications->count() > 1)
                <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                    <div class="d-flex align-items-center">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1.5 fs-7 me-2">
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
                <div class="google-notif-card card border-0 shadow-sm rounded-3 mb-3 overflow-hidden border-start border-4 border-{{ $notif->is_important ? 'danger' : 'primary' }}"
                     style="background: {{ $notif->is_important ? '#fff8f8' : '#f8fafc' }}; transition: all 0.2s ease-in-out;"
                     wire:key="top-notif-{{ $notif->id }}">
                    <div class="card-body p-3.5">
                        <!-- Ligne 1 : Icône sur sa propre ligne -->
                        <div class="mb-2">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center shadow-xs" 
                                 style="width: 40px; height: 40px; background: {{ $notif->is_important ? '#fee2e2' : '#e8f0fe' }}; color: {{ $notif->is_important ? '#dc2626' : '#1a73e8' }};">
                                <i class="bi {{ $notif->is_important ? 'bi-exclamation-circle-fill' : 'bi-bell-fill' }} fs-5"></i>
                            </div>
                        </div>

                        <!-- Ligne 2 : Importance sur la seconde ligne -->
                        <div class="mb-2">
                            <span class="badge bg-{{ $notif->is_important ? 'danger' : 'primary' }} px-2.5 py-1 rounded-pill" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                {{ $notif->is_important ? 'IMPORTANT' : 'ANNONCE' }}
                            </span>
                        </div>

                        <!-- Ligne 3 : Titre sur la troisième ligne -->
                        <div class="mb-2">
                            <h5 class="fw-bold text-dark mb-0" style="font-size: 1.05rem; font-family: system-ui, -apple-system, sans-serif;">
                                {!! $notif->title !!}
                            </h5>
                        </div>

                        <!-- Ligne 4 : Message Justifié -->
                        <div class="mb-3 text-secondary" style="font-size: 0.92rem; line-height: 1.6; white-space: pre-line; text-align: justify; color: #374151;">
                            {!! $notif->message !!}
                        </div>

                        <!-- Ligne 5 : Date et heure de publication -->
                        <div class="small text-muted mb-1" style="font-size: 0.8rem;">
                            <i class="bi bi-calendar-event me-1.5 text-primary"></i>
                            <strong>Date & heure :</strong> {{ $notif->created_at->format('d/m/Y à H:i') }}
                        </div>

                        <!-- Ligne 6 : Temps écoulé -->
                        <div class="small text-muted mb-1" style="font-size: 0.8rem;">
                            <i class="bi bi-clock-history me-1.5 text-primary"></i>
                            <strong>Temps écoulé :</strong> {{ $notif->created_at->diffForHumans() }}
                        </div>

                        <!-- Ligne 7 : Expéditeur -->
                        <div class="small text-muted mb-3" style="font-size: 0.8rem;">
                            <i class="bi bi-person-circle me-1.5 text-primary"></i>
                            <strong>Expéditeur :</strong> {{ $notif->sender->name ?? 'Administration' }}
                        </div>

                        <!-- Ligne 8 : Bouton d'action sur une nouvelle ligne -->
                        <div>
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
                                    Traitement...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
