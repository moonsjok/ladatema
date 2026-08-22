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
        <!-- Formulaire de création de notification -->
        <div class="col-lg-5 mb-4">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-primary text-white py-3 rounded-top-4">
                    <h5 class="card-title mb-0 fw-bold fs-6">
                        <i class="bi bi-send-plus-fill me-2"></i> Envoyer une notification
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('app-notifications.store') }}" method="POST" id="notificationForm">
                        @csrf

                        <!-- Titre -->
                        <div class="mb-3">
                            <label for="title" class="form-label fw-bold">Titre du message <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" placeholder="Ex: Maintenance système, Annonce..." required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Cible / Destinataires -->
                        <div class="mb-3">
                            <label for="target_type" class="form-label fw-bold">Destinataires <span class="text-danger">*</span></label>
                            <select class="form-select @error('target_type') is-invalid @enderror" id="target_type" name="target_type" required onchange="toggleUserSelect()">
                                <option value="all" {{ old('target_type') == 'all' ? 'selected' : '' }}>Tous les utilisateurs (Global)</option>
                                <option value="student" {{ old('target_type') == 'student' ? 'selected' : '' }}>Tous les Étudiants uniquement</option>
                                @if($authUser->role === 'dev')
                                    <option value="owner" {{ old('target_type') == 'owner' ? 'selected' : '' }}>Tous les Propriétaires uniquement</option>
                                @endif
                                <option value="user" {{ old('target_type') == 'user' ? 'selected' : '' }}>Un utilisateur / étudiant spécifique</option>
                            </select>
                            @error('target_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Sélection de l'utilisateur spécifique (masqué par défaut) -->
                        <div class="mb-3" id="singleUserContainer" style="display: {{ old('target_type') == 'user' ? 'block' : 'none' }};">
                            <label for="target_user_id" class="form-label fw-bold">Sélectionner l'utilisateur <span class="text-danger">*</span></label>
                            <select class="form-select @error('target_user_id') is-invalid @enderror" id="target_user_id" name="target_user_id">
                                <option value="">-- Choisir un utilisateur --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('target_user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->prenoms }} {{ $user->nom }} ({{ $user->email }}) - Role: {{ ucfirst($user->role) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('target_user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-info mt-1">
                                <i class="bi bi-envelope-fill me-1"></i> Cet utilisateur recevra un <strong>e-mail direct</strong> en plus de l'affichage sur son tableau de bord.
                            </div>
                        </div>

                        <!-- Option Message Important -->
                        <div class="form-check form-switch mb-3 p-3 bg-light rounded-3 border">
                            <input class="form-check-input ms-0 me-2" type="checkbox" role="switch" id="is_important" name="is_important" value="1" {{ old('is_important') ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-dark" for="is_important">
                                <i class="bi bi-exclamation-triangle-fill text-warning me-1"></i> Message Important (Bannière d'alerte prioritaire en haut du tableau de bord)
                            </label>
                        </div>

                        <!-- Contenu du message -->
                        <div class="mb-3">
                            <label for="message" class="form-label fw-bold">Contenu du message <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="5" placeholder="Rédigez votre message..." required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold">
                                <i class="bi bi-send-fill me-1"></i> Publier la notification
                            </button>
                        </div>
                    </form>
                </div>
            </div>
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
