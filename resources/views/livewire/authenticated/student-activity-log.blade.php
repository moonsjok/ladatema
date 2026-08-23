<div id="student-activity-log-root" class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-primary text-white py-3 rounded-top-4">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">
                <i class="bi bi-activity me-2"></i> Journal des Activités
            </h5>
            <span class="badge bg-white text-primary rounded-pill px-3 py-1.5 small fw-semibold">
                {{ $activities->count() }} / {{ $totalCount }}
            </span>
        </div>
    </div>

    <div class="card-body p-3" style="max-height: 650px; overflow-y: auto;" id="activity-log-scroll-container">
        @if ($activities->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-clock-history fs-1 d-block mb-2 text-secondary"></i>
                <p class="mb-0 small fw-semibold">Aucune activité enregistrée pour le moment.</p>
            </div>
        @else
            <div class="activity-timeline">
                @foreach ($activities as $log)
                    <div class="card border-0 shadow-sm rounded-3 mb-3 p-3 bg-white border-start border-4 border-{{ $log['color'] }}">
                        <!-- Ligne 1: Icône + Titre -->
                        <div class="fw-bold text-dark fs-6 mb-1">
                            <i class="bi {{ $log['icon'] }} text-{{ $log['color'] }} me-2 fs-5"></i>
                            {{ $log['title'] }}
                        </div>

                        <!-- Ligne 2: Statut -->
                        <div class="mb-2">
                            <span class="badge bg-{{ $log['color'] }}-subtle text-{{ $log['color'] }} border border-{{ $log['color'] }} rounded-pill px-2.5 py-1">
                                {{ $log['status'] }}
                            </span>
                        </div>

                        <!-- Ligne 3: Description -->
                        <div class="small text-secondary mb-2">
                            {{ $log['description'] }}
                        </div>

                        <!-- Ligne 4: Date -->
                        <div class="small text-muted border-top pt-2 mt-1" style="font-size: 0.8rem;">
                            <i class="bi bi-clock me-1 text-primary"></i>
                            @if (isset($log['date']))
                                {{ is_string($log['date']) ? \Carbon\Carbon::parse($log['date'])->diffForHumans() : $log['date']->diffForHumans() }}
                                <span class="text-secondary opacity-75 ms-1">
                                    ({{ is_string($log['date']) ? \Carbon\Carbon::parse($log['date'])->format('d/m/Y à H:i') : $log['date']->format('d/m/Y à H:i') }})
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Déclencheur Infinite Load / Bouton Charger plus -->
            @if ($hasMore)
                <div class="text-center my-3 pt-2" x-data x-intersect="$wire.loadMore()">
                    <button class="btn btn-sm btn-outline-primary rounded-pill px-4 shadow-sm fw-semibold" 
                            wire:click="loadMore" 
                            wire:loading.attr="disabled">
                        <span wire:loading.remove>
                            <i class="bi bi-arrow-down-circle me-1"></i> Charger plus d'activités...
                        </span>
                        <span wire:loading>
                            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                            Chargement en cours...
                        </span>
                    </button>
                </div>
            @else
                <div class="text-center text-muted small py-2 mt-2 border-top">
                    <i class="bi bi-check-circle me-1 text-success"></i> Vous avez atteint le début du journal.
                </div>
            @endif
        @endif
    </div>
</div>
