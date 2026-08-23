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
                        <div class="list-group list-group-flush">
                            @foreach($notifications as $notif)
                                @php
                                    $isRead = in_array($notif->id, $readIds);
                                @endphp
                                <div class="list-group-item p-4 border-bottom {{ $isRead ? 'bg-white' : 'bg-primary-subtle border-start border-4 border-primary' }}">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="d-flex align-items-center flex-wrap gap-2">
                                            <span class="badge bg-{{ $notif->is_important ? 'danger' : 'primary' }} px-2.5 py-1 rounded-pill">
                                                {{ $notif->is_important ? 'URGENT' : 'ANNONCE' }}
                                            </span>
                                            <h5 class="fw-bold mb-0 text-dark">
                                                {!! $notif->title !!}
                                            </h5>
                                        </div>
                                        <span class="badge bg-{{ $isRead ? 'light text-secondary border' : 'success' }} rounded-pill px-3 py-1.5">
                                            <i class="bi bi-{{ $isRead ? 'check2-all text-success' : 'bell-fill' }} me-1"></i>
                                            {{ $isRead ? 'Déjà lue' : 'Nouvelle' }}
                                        </span>
                                    </div>

                                    <div class="text-secondary my-3" style="font-size: 0.95rem; line-height: 1.6; white-space: pre-line;">
                                        {!! $notif->message !!}
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                        <div class="small text-muted">
                                            <i class="bi bi-clock me-1 text-primary"></i> {{ $notif->created_at->format('d/m/Y à H:i') }} ({{ $notif->created_at->diffForHumans() }})
                                            <span class="mx-2">•</span>
                                            <i class="bi bi-person me-1"></i> De : {{ $notif->sender->name ?? 'Administration' }}
                                        </div>

                                        @if(!$isRead)
                                            <form action="{{ route('app-notifications.read', $notif->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold">
                                                    <i class="bi bi-check-lg me-1"></i> Marquer comme lue
                                                </button>
                                            </form>
                                        @endif
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
