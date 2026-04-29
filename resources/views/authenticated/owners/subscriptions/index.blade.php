@extends('layouts.authenticated.owners.index')

@section('page-title', "Gestion des souscriptions")

@section('dashboard-content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Gestion des souscriptions</h1>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#searchStudentModal">
                    <i class="bi bi-search me-2"></i>Rechercher un étudiant
                </button>
                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#bulkUpdateModal">
                    <i class="bi bi-clock-history me-2"></i>Définir durée par défaut
                </button>
                <a href="{{ route('subscriptions.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Nouvelle souscription
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Utilisateur</th>
                                <th>Type</th>
                                <th>Contenu</th>
                                <th>Prix (FCFA)</th>
                                <th>Durée</th>
                                <th>Expiration</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subscriptions as $subscription)
                                <tr>
                                    <td>{{ $subscription->id }}</td>
                                    <td>
                                        <div>
                                            <strong>{{ $subscription->user->name }}</strong><br>
                                            <small class="text-muted">{{ $subscription->user->email }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info text-capitalize">{{ $subscription->type }}</span>
                                    </td>
                                    <td>
                                        @if($subscription->formation)
                                            {{ $subscription->formation->title }}
                                        @elseif($subscription->course)
                                            {{ $subscription->course->title }}
                                        @elseif($subscription->chapter)
                                            {{ $subscription->chapter->title }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>{{ number_format($subscription->price) }} FCFA</td>
                                    <td>{{ $subscription->duration_in_days }} jours</td>
                                    <td>
                                        @if($subscription->expires_at)
                                            <div>
                                                {{ $subscription->expires_at->format('d/m/Y H:i') }}
                                                <br>
                                                <small class="text-muted">
                                                    {{ $subscription->days_remaining }} jours restants
                                                </small>
                                            </div>
                                        @else
                                            <span class="text-muted">Non définie</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($subscription->isExpired())
                                            <span class="badge bg-danger">Expirée</span>
                                        @elseif($subscription->isActive())
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-warning">En attente</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('subscriptions.edit', $subscription) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            
                                            @if(!$subscription->isExpired())
                                                <button type="button" class="btn btn-sm btn-outline-success" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#extendModal{{ $subscription->id }}" 
                                                        title="Étendre">
                                                    <i class="bi bi-calendar-plus"></i>
                                                </button>
                                            @endif
                                            
                                            <form action="{{ route('subscriptions.destroy', $subscription) }}" 
                                                  method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette souscription ?')"
                                                        title="Supprimer">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <i class="bi bi-inbox display-4 text-muted"></i>
                                        <p class="text-muted mt-2">Aucune souscription trouvée</p>
                                        <a href="{{ route('subscriptions.create') }}" class="btn btn-primary">
                                            Créer une souscription
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($subscriptions->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $subscriptions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modals pour l'extension des souscriptions -->
    @foreach($subscriptions as $subscription)
        @if(!$subscription->isExpired())
            <div class="modal fade" id="extendModal{{ $subscription->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Étendre la souscription</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('subscriptions.extend', $subscription) }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="additional_days_{{ $subscription->id }}" class="form-label">
                                        Nombre de jours à ajouter
                                    </label>
                                    <input type="number" class="form-control" 
                                           id="additional_days_{{ $subscription->id }}" 
                                           name="additional_days" 
                                           min="1" max="365" value="30" required>
                                    <div class="form-text">
                                        Entre 1 et 365 jours
                                    </div>
                                </div>
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    La souscription de {{ $subscription->user->name }} 
                                    @if($subscription->expires_at)
                                        expire actuellement le {{ $subscription->expires_at->format('d/m/Y') }}
                                    @else
                                        n'a pas de date d'expiration définie
                                    @endif
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-calendar-plus me-2"></i>Étendre
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    <!-- Modal pour rechercher un étudiant -->
    <div class="modal fade" id="searchStudentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-search me-2"></i>
                        Rechercher un étudiant
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="studentSearch" class="form-label fw-bold">
                            <i class="bi bi-person me-2 text-info"></i>
                            Rechercher par email ou téléphone
                        </label>
                        <input type="text" class="form-control" id="studentSearch" 
                               placeholder="Entrez l'email ou le téléphone de l'étudiant..." 
                               autocomplete="off">
                        <div class="form-text">
                            Tapez au moins 3 caractères pour lancer la recherche
                        </div>
                    </div>

                    <div id="searchResults" class="d-none">
                        <h6 class="text-primary mb-3">Résultats de recherche</h6>
                        <div id="studentsList"></div>
                    </div>

                    <div id="noResults" class="alert alert-warning d-none">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Aucun étudiant trouvé pour cette recherche.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal pour la mise à jour groupée des durées -->
    <div class="modal fade" id="bulkUpdateModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-clock-history me-2"></i>
                        Définir durée par défaut
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('subscriptions.bulkUpdateDuration') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-4">
                            <label for="bulk_duration" class="form-label fw-bold">
                                <i class="bi bi-calendar me-2 text-warning"></i>
                                Durée (en jours) *
                            </label>
                            <input type="number" class="form-control" id="bulk_duration" name="duration_in_days" 
                                   value="30" min="1" max="365" required>
                            <div class="form-text">
                                Entre 1 et 365 jours
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="only_without_expiration" 
                                       name="only_without_expiration" value="1" checked>
                                <label class="form-check-label" for="only_without_expiration">
                                    <i class="bi bi-filter-circle me-2 text-info"></i>
                                    Uniquement les souscriptions sans date d'expiration
                                </label>
                            </div>
                            <div class="form-text">
                                Cochez cette case pour ne mettre à jour que les souscriptions 
                                qui n'ont pas encore de date d'expiration définie.
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Attention :</strong> Cette action va mettre à jour la date d'expiration 
                            de {{ $subscriptions->total() }} souscription(s) en fonction de leur date de création.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-clock-history me-2"></i>
                            Appliquer la durée
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal pour confirmer la modification de souscription -->
    <div class="modal fade" id="confirmUpdateModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-check-circle me-2 text-warning"></i>
                        Confirmer la modification
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <h6 class="alert-heading">Détails de la souscription</h6>
                        <div id="subscriptionDetails"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="newDuration" class="form-label fw-bold">
                            <i class="bi bi-calendar me-2 text-warning"></i>
                            Nouvelle durée (en jours) *
                        </label>
                        <input type="number" class="form-control" id="newDuration" 
                               min="1" max="365" required>
                        <div class="form-text">
                            Entre 1 et 365 jours
                        </div>
                    </div>

                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Confirmation requise :</strong> Cette action va mettre à jour la date d'expiration 
                        de cette souscription. Êtes-vous sûr de vouloir continuer ?
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-warning" id="confirmUpdateBtn">
                        <i class="bi bi-check-circle me-2"></i>
                        Confirmer la modification
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let searchTimeout;
        let selectedSubscription = null;

        // Recherche d'étudiant en temps réel
        document.getElementById('studentSearch').addEventListener('input', function(e) {
            const search = e.target.value.trim();
            
            clearTimeout(searchTimeout);
            
            if (search.length < 3) {
                document.getElementById('searchResults').classList.add('d-none');
                document.getElementById('noResults').classList.add('d-none');
                return;
            }

            searchTimeout = setTimeout(() => {
                fetch(`{{ route('subscriptions.searchStudent') }}?search=${encodeURIComponent(search)}`)
                    .then(response => response.json())
                    .then(data => {
                        const resultsDiv = document.getElementById('searchResults');
                        const noResultsDiv = document.getElementById('noResults');
                        const studentsList = document.getElementById('studentsList');
                        
                        if (data.users && data.users.length > 0) {
                            let html = '';
                            data.users.forEach(user => {
                                const subscriptionsHtml = user.subscriptions && user.subscriptions.length > 0 ? 
                                    user.subscriptions.map(sub => `
                                        <div class="subscription-item border rounded p-2 mb-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong class="text-primary">${sub.formation ? sub.formation.title : (sub.course ? sub.course.title : (sub.chapter ? sub.chapter.title : 'N/A'))}</strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        Durée actuelle: ${sub.duration_in_days || 'Non définie'} jours
                                                        ${sub.expires_at ? `| Expire: ${sub.expires_at}` : ''}
                                                    </small>
                                                </div>
                                                <button class="btn btn-sm btn-outline-warning" 
                                                        onclick="showUpdateModal(${user.id}, ${sub.id}, '${user.name}', '${sub.formation ? sub.formation.title : (sub.course ? sub.course.title : (sub.chapter ? sub.chapter.title : 'N/A'))}', ${sub.duration_in_days || 0})">
                                                    <i class="bi bi-pencil"></i> Modifier
                                                </button>
                                            </div>
                                        </div>
                                    `).join('') : 
                                    '<p class="text-muted">Aucune souscription trouvée</p>';
                                
                                html += `
                                    <div class="student-item border rounded p-3 mb-3">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <strong class="text-primary">${user.name}</strong><br>
                                                <small class="text-muted">${user.email}</small>
                                                ${user.phone ? `<br><small class="text-muted">📱 ${user.phone}</small>` : ''}
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            ${subscriptionsHtml}
                                        </div>
                                    </div>
                                `;
                            });
                            
                            studentsList.innerHTML = html;
                            resultsDiv.classList.remove('d-none');
                            noResultsDiv.classList.add('d-none');
                        } else {
                            resultsDiv.classList.add('d-none');
                            noResultsDiv.classList.remove('d-none');
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors de la recherche:', error);
                    });
            }, 500);
        });

        function showUpdateModal(userId, subscriptionId, userName, contentTitle, currentDuration) {
            selectedSubscription = {
                userId: userId,
                subscriptionId: subscriptionId,
                userName: userName,
                contentTitle: contentTitle,
                currentDuration: currentDuration
            };

            document.getElementById('subscriptionDetails').innerHTML = `
                <strong>Étudiant:</strong> ${userName}<br>
                <strong>Contenu:</strong> ${contentTitle}<br>
                <strong>Durée actuelle:</strong> ${currentDuration} jours
            `;

            document.getElementById('newDuration').value = currentDuration;
            
            const modal = new bootstrap.Modal(document.getElementById('confirmUpdateModal'));
            modal.show();
        }

        document.getElementById('confirmUpdateBtn').addEventListener('click', function() {
            if (!selectedSubscription) return;

            const newDuration = document.getElementById('newDuration').value;
            
            if (!newDuration || newDuration < 1 || newDuration > 365) {
                alert('Veuillez entrer une durée valide entre 1 et 365 jours');
                return;
            }

            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Modification...';

            fetch('{{ route('subscriptions.updateStudentDuration') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    user_id: selectedSubscription.userId,
                    subscription_id: selectedSubscription.subscriptionId,
                    duration_in_days: parseInt(newDuration)
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    bootstrap.Modal.getInstance(document.getElementById('confirmUpdateModal')).hide();
                    document.getElementById('studentSearch').value = '';
                    document.getElementById('searchResults').classList.add('d-none');
                    location.reload();
                } else {
                    alert(data.error || 'Erreur lors de la mise à jour');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de la mise à jour');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Confirmer la modification';
            });
        });
    </script>
@endsection
