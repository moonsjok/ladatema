<div>
    <!-- Champ de recherche -->
    <div class="mb-4">
        <div class="input-group">
            <input 
                type="text" 
                wire:model.live.debounce.500ms="search" 
                class="form-control" 
                placeholder="Rechercher un étudiant par email..." 
                autocomplete="off"
            >
            <span class="input-group-text">
                <i class="bi bi-search"></i>
            </span>
        </div>
        @if($loading)
            <div class="mt-2 text-center">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Recherche en cours...</span>
                </div>
                <small class="text-muted ms-2">Recherche en cours...</small>
            </div>
        @endif
    </div>

    <!-- Résultats de recherche -->
    @if($search && strlen($search) >= 3)
        @if($users->count() > 0)
            <div class="list-group">
                @foreach($users as $user)
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong class="text-primary">{{ $user->name }}</strong><br>
                                <small class="text-muted">{{ $user->email }}</small>
                                @if($user->phone)
                                    <br><small class="text-muted">Téléphone: {{ $user->phone }}</small>
                                @endif
                            </div>
                            <div>
                                <button 
                                    class="btn btn-sm btn-outline-primary" 
                                    wire:click="selectUser({{ $user->id }})"
                                >
                                    <i class="bi bi-pencil"></i> Modifier
                                </button>
                            </div>
                        </div>
                        
                        @if($user->souscriptions && $user->souscriptions->count() > 0)
                            <div class="mt-2 p-2 bg-light rounded">
                                <small class="text-muted fw-bold">Souscriptions :</small>
                                @foreach($user->souscriptions as $subscription)
                                    <div class="d-flex justify-content-between align-items-center mt-1 p-1 border rounded">
                                        <div>
                                            <strong>{{ $subscription->formation ? $subscription->formation->title : ($subscription->course ? $subscription->course->title : ($subscription->chapter ? $subscription->chapter->title : 'N/A')) }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                Durée: {{ $subscription->duration_in_days ?? 'Non définie' }} jours
                                                @if($subscription->expires_at)
                                                    | Expire: {{ $subscription->expires_at }}
                                                @endif
                                            </small>
                                        </div>
                                        <button 
                                            class="btn btn-sm btn-outline-warning" 
                                            wire:click="selectUser({{ $user->id }})"
                                        >
                                            <i class="bi bi-pencil"></i> Modifier
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Aucun étudiant trouvé pour "{{ $search }}"
            </div>
        @endif
    @endif

    <!-- Instructions -->
    @if(!$search || strlen($search) < 3)
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            Tapez au moins 3 caractères pour lancer la recherche d'étudiant par email.
        </div>
    @endif
</div>
