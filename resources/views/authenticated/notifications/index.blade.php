@extends('layouts.authenticated.owners.index')

@section('page-title', 'Gestion des Notifications')

@section('main-content')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800 fw-bold">
            <i class="bi bi-bell-fill text-primary me-2"></i> Gestion des Notifications & Messages
        </h2>
    </div>

    <div class="row">
        <!-- Assistant Livewire d'envoi de notification (Multi-étapes) -->
        <div class="col-lg-5 mb-4">
            @livewire('authenticated.notification-manager')
        </div>

        <!-- Historique des notifications envoyées -->
        <div class="col-lg-7 mb-4">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-dark text-white py-3 rounded-top-4">
                    <h5 class="card-title mb-0 fw-bold fs-6">
                        <i class="bi bi-clock-history me-2"></i> Historique des messages publiés
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($notifications->isEmpty())
                        <div class="p-4 text-center text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            <p class="mb-0">Aucune notification publiée pour le moment.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Cible</th>
                                        <th>Message</th>
                                        <th>Statut</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($notifications as $notif)
                                        <tr>
                                            <td>
                                                <small class="fw-bold d-block">{{ $notif->created_at->format('d/m/Y H:i') }}</small>
                                                <small class="text-muted">Par {{ $notif->sender->name ?? 'Admin' }}</small>
                                            </td>
                                            <td>
                                                @if($notif->target_type === 'all')
                                                    <span class="badge bg-secondary">Tous</span>
                                                @elseif($notif->target_type === 'student')
                                                    <span class="badge bg-info text-dark">Étudiants</span>
                                                @elseif($notif->target_type === 'owner')
                                                    <span class="badge bg-dark">Propriétaires</span>
                                                @elseif($notif->target_type === 'user')
                                                    <span class="badge bg-primary">Spécifique</span>
                                                    <small class="d-block text-muted">{{ $notif->targetUser->name ?? 'Utilisateur' }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <strong class="d-block text-dark">{{ $notif->title }}</strong>
                                                <small class="text-muted text-truncate d-inline-block" style="max-width: 220px;">
                                                    {{ Str::limit($notif->message, 60) }}
                                                </small>
                                            </td>
                                            <td>
                                                @if($notif->is_important)
                                                    <span class="badge bg-danger"><i class="bi bi-exclamation-circle-fill"></i> Important</span>
                                                @else
                                                    <span class="badge bg-light text-dark border">Normal</span>
                                                @endif
                                                <small class="d-block text-muted mt-1"><i class="bi bi-eye"></i> {{ $notif->reads_count }} vue(s)</small>
                                            </td>
                                            <td class="text-end">
                                                <form action="{{ route('app-notifications.destroy', $notif->id) }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer cette notification ?');" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="p-3 d-flex justify-content-center">
                            {{ $notifications->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleUserSelect() {
        const targetType = document.getElementById('target_type').value;
        const singleContainer = document.getElementById('singleUserContainer');
        const userSelect = document.getElementById('target_user_id');

        if (targetType === 'user') {
            singleContainer.style.display = 'block';
            userSelect.required = true;
        } else {
            singleContainer.style.display = 'none';
            userSelect.required = false;
            userSelect.value = '';
        }
    }
</script>
@endsection
