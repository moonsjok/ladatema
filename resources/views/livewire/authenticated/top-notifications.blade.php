<div id="top-notifications-root" wire:poll.15s>
    @if($notifications && $notifications->count() > 0)
        <div class="row px-2 mb-3">
            <div class="col-12">
                @foreach($notifications as $notif)
                    <div class="alert alert-{{ $notif->is_important ? 'danger' : 'info' }} alert-dismissible fade show shadow-sm rounded-4 border-0 p-3 mb-2" 
                         role="alert" 
                         wire:key="top-notif-{{ $notif->id }}">
                        <div class="d-flex align-items-start">
                            <div class="me-3 fs-3 text-{{ $notif->is_important ? 'danger' : 'info' }}">
                                <i class="bi {{ $notif->is_important ? 'bi-exclamation-triangle-fill' : 'bi-bell-fill' }}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-1">
                                    <h5 class="alert-heading fw-bold mb-0 me-2">{{ $notif->title }}</h5>
                                    @if($notif->is_important)
                                        <span class="badge bg-danger rounded-pill">Important</span>
                                    @endif
                                </div>
                                <p class="mb-0 text-dark" style="white-space: pre-line;">{{ $notif->message }}</p>
                                <small class="text-muted d-block mt-1" style="font-size: 0.8rem;">
                                    <i class="bi bi-clock me-1"></i> Envoyé {{ $notif->created_at->diffForHumans() }} par {{ $notif->sender->name ?? 'Administration' }}
                                </small>
                            </div>
                            <div>
                                <button type="button" 
                                        class="btn btn-sm btn-outline-secondary rounded-pill ms-2 fw-semibold" 
                                        wire:click="markAsRead({{ $notif->id }})"
                                        wire:loading.attr="disabled"
                                        title="Marquer comme lu">
                                    <span wire:loading.remove wire:target="markAsRead({{ $notif->id }})">
                                        <i class="bi bi-check-lg me-1"></i> Marquer comme lu
                                    </span>
                                    <span wire:loading wire:target="markAsRead({{ $notif->id }})">
                                        <span class="spinner-border spinner-border-sm me-1" role="status"></span> Traitement...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
