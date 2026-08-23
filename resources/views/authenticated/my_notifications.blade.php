@extends($user->role === 'student' ? 'layouts.authenticated.students.index' : 'layouts.authenticated.owners.index')

@section('page-title', 'Mes Notifications')

@section('dashboard-content')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800 fw-bold">
            <i class="bi bi-bell-fill text-primary me-2"></i> Boîte de réception des Notifications
        </h2>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-primary text-white py-3 rounded-top-4">
                    <h5 class="card-title mb-0 fw-bold fs-6">
                        <i class="bi bi-inbox-fill me-2"></i> Historique de toutes vos notifications reçues
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($notifications->isEmpty())
                        <div class="p-5 text-center text-muted">
                            <i class="bi bi-bell-slash fs-1 d-block mb-2 text-secondary"></i>
                            <p class="mb-0 fw-semibold">Aucune notification reçue pour le moment.</p>
                        </div>
                    @else
                        @php
                            $isSingleNotif = $notifications->count() === 1;
                        @endphp
                        <div class="accordion accordion-flush" id="myNotificationsAccordion">
                            @foreach($notifications as $notif)
                                @php
                                    $isRead = in_array($notif->id, $readIds);
                                    // Expand par défaut uniquement s'il n'y a qu'un seul élément
                                    $shouldExpand = $isSingleNotif;
                                @endphp
                                <div class="accordion-item border-bottom {{ $isRead ? 'bg-white' : 'bg-primary-subtle border-start border-4 border-primary' }}">
                                    <h2 class="accordion-header" id="headingNotif{{ $notif->id }}">
                                        <button class="accordion-button p-4 text-start {{ $shouldExpand ? '' : 'collapsed' }}" 
                                                type="button" 
                                                data-bs-toggle="collapse" 
                                                data-bs-target="#collapseNotif{{ $notif->id }}" 
                                                aria-expanded="{{ $shouldExpand ? 'true' : 'false' }}" 
                                                aria-controls="collapseNotif{{ $notif->id }}">
                                            <div class="w-100 pe-3">
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
                                                    @if($isRead)
                                                        <span class="badge bg-light text-secondary border rounded-pill px-2.5 py-1 ms-1">
                                                            <i class="bi bi-check2-all text-success me-1"></i> Déjà lue
                                                        </span>
                                                    @else
                                                        <span class="badge bg-success rounded-pill px-2.5 py-1 ms-1">
                                                            <i class="bi bi-bell-fill me-1"></i> Nouvelle
                                                        </span>
                                                    @endif
                                                </div>

                                                <!-- Ligne 3 : Titre sur la troisième ligne -->
                                                <div>
                                                    <h5 class="fw-bold text-dark mb-0" style="font-size: 1.05rem;">
                                                        {!! $notif->title !!}
                                                    </h5>
                                                </div>
                                            </div>
                                        </button>
                                    </h2>

                                    <div id="collapseNotif{{ $notif->id }}" 
                                         class="accordion-collapse collapse {{ $shouldExpand ? 'show' : '' }}" 
                                         aria-labelledby="headingNotif{{ $notif->id }}" 
                                         data-bs-parent="#myNotificationsAccordion">
                                        <div class="accordion-body p-4 pt-2">
                                            <!-- Ligne 4 : Message Justifié -->
                                            <div class="mb-3 text-secondary" style="font-size: 0.95rem; line-height: 1.6; white-space: pre-line; text-align: justify; color: #374151;">
                                                {!! $notif->message !!}
                                            </div>

                                            <!-- Ligne 5 : Date et heure de publication -->
                                            <div class="small text-muted mb-1" style="font-size: 0.82rem;">
                                                <i class="bi bi-calendar-event me-1.5 text-primary"></i>
                                                <strong>Date & heure :</strong> {{ $notif->created_at->format('d/m/Y à H:i') }}
                                            </div>

                                            <!-- Ligne 6 : Temps écoulé -->
                                            <div class="small text-muted mb-1" style="font-size: 0.82rem;">
                                                <i class="bi bi-clock-history me-1.5 text-primary"></i>
                                                <strong>Temps écoulé :</strong> {{ $notif->created_at->diffForHumans() }}
                                            </div>

                                            <!-- Ligne 7 : Expéditeur -->
                                            <div class="small text-muted mb-3" style="font-size: 0.82rem;">
                                                <i class="bi bi-person-circle me-1.5 text-primary"></i>
                                                <strong>Expéditeur :</strong> {{ $notif->sender->name ?? 'Administration' }}
                                            </div>

                                            <!-- Ligne 8 : Bouton d'action -->
                                            @if(!$isRead)
                                                <div>
                                                    <form action="{{ route('app-notifications.read', $notif->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold">
                                                            <i class="bi bi-check-lg me-1"></i> Marquer comme lue
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="p-3 border-top">
                            {{ $notifications->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
