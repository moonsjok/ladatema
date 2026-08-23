@extends('layouts.authenticated.owners.index')

@section('page-title', 'Gestion des Notifications')

@section('dashboard-content')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800 fw-bold">
            <i class="bi bi-bell-fill text-primary me-2"></i> Gestion des Notifications & Messages
        </h2>
    </div>

    <div class="row">
        <!-- Assistant Livewire d'envoi de notification (Multi-étapes) -->
        <div class="col-md-12 mb-2">
            @livewire('authenticated.notification-manager')
        </div>
    </div>
    <div class="row">
        
        <!-- Historique des notifications envoyées -->
        <div class="col-md-12 mb-4">
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
                                        <th>Message</th>
                                        <th>Cible</th>
                                        <th>Date</th>
                                        <th>Lectures</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($notifications as $notification)
                                        <tr>
                                            <td>
                                                <div class="fw-bold text-dark">
                                                    @if($notification->is_important)
                                                        <span class="badge bg-danger me-1">Important</span>
                                                    @endif
                                                    {{ $notification->title }}
                                                </div>
                                                <div class="small text-muted text-truncate" style="max-width: 250px;">
                                                    {{ $notification->message }}
                                                </div>
                                            </td>
                                            <td>
                                                @if($notification->target_type === 'all')
                                                    <span class="badge bg-primary rounded-pill">Tous</span>
                                                @elseif($notification->target_type === 'student')
                                                    <span class="badge bg-success rounded-pill">Étudiants</span>
                                                @elseif($notification->target_type === 'owner')
                                                    <span class="badge bg-warning text-dark rounded-pill">Owners</span>
                                                @elseif($notification->target_type === 'dev')
                                                    <span class="badge bg-danger rounded-pill">Devs</span>
                                                @elseif($notification->target_type === 'user')
                                                    <span class="badge bg-info text-dark rounded-pill">
                                                        {{ $notification->targetUser->name ?? 'Utilisateur' }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="small text-muted">
                                                {{ $notification->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary rounded-pill">
                                                    <i class="bi bi-eye-fill me-1"></i> {{ $notification->reads_count }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <form action="{{ route('app-notifications.destroy', $notification->id) }}" method="POST" class="d-inline" id="delete-notif-form-{{ $notification->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-circle" title="Supprimer" onclick="confirmDeleteNotif({{ $notification->id }})">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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

<script>
    function confirmDeleteNotif(id) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Supprimer la notification ?',
                text: "Cette action est définitive !",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Oui, supprimer !',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-notif-form-' + id).submit();
                }
            });
        } else {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette notification ?')) {
                document.getElementById('delete-notif-form-' + id).submit();
            }
        }
    }
</script>
@endsection
