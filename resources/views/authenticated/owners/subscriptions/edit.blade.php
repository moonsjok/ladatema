@extends('layouts.authenticated.owners.index')

@section('page-title', "Modifier la souscription")

@section('dashboard-content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Modifier la souscription #{{ $subscription->id }}</h1>
            <a href="{{ route('subscriptions.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Retour à la liste
            </a>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <h5 class="alert-heading">Erreur de validation</h5>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form action="{{ route('subscriptions.update', $subscription) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <!-- Informations utilisateur -->
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label for="user_id" class="form-label fw-bold">
                                    <i class="bi bi-person me-2 text-primary"></i>
                                    Utilisateur *
                                </label>
                                <select class="form-select" id="user_id" name="user_id" required>
                                    <option value="">Sélectionner un utilisateur</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id', $subscription->user_id) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Type de souscription -->
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label for="type" class="form-label fw-bold">
                                    <i class="bi bi-tag me-2 text-primary"></i>
                                    Type de souscription *
                                </label>
                                <select class="form-select" id="type" name="type" required onchange="updateContentSelection()">
                                    <option value="">Sélectionner un type</option>
                                    <option value="formation" {{ old('type', $subscription->type) === 'formation' ? 'selected' : '' }}>Formation</option>
                                    <option value="course" {{ old('type', $subscription->type) === 'course' ? 'selected' : '' }}>Cours</option>
                                    <option value="chapter" {{ old('type', $subscription->type) === 'chapter' ? 'selected' : '' }}>Chapitre</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Contenu selon le type -->
                        <div class="col-md-6">
                            <div class="mb-4" id="formation-group" style="display: none;">
                                <label for="formation_id" class="form-label fw-bold">
                                    <i class="bi bi-mortarboard me-2 text-primary"></i>
                                    Formation *
                                </label>
                                <select class="form-select" id="formation_id" name="formation_id">
                                    <option value="">Sélectionner une formation</option>
                                    @foreach($formations as $formation)
                                        <option value="{{ $formation->id }}" {{ old('formation_id', $subscription->formation_id) == $formation->id ? 'selected' : '' }}>
                                            {{ $formation->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-4" id="course-group" style="display: none;">
                                <label for="course_id" class="form-label fw-bold">
                                    <i class="bi bi-book me-2 text-primary"></i>
                                    Cours *
                                </label>
                                <select class="form-select" id="course_id" name="course_id">
                                    <option value="">Sélectionner un cours</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ old('course_id', $subscription->course_id) == $course->id ? 'selected' : '' }}>
                                            {{ $course->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-4" id="chapter-group" style="display: none;">
                                <label for="chapter_id" class="form-label fw-bold">
                                    <i class="bi bi-file-text me-2 text-primary"></i>
                                    Chapitre *
                                </label>
                                <select class="form-select" id="chapter_id" name="chapter_id">
                                    <option value="">Sélectionner un chapitre</option>
                                    @foreach($chapters as $chapter)
                                        <option value="{{ $chapter->id }}" {{ old('chapter_id', $subscription->chapter_id) == $chapter->id ? 'selected' : '' }}>
                                            {{ $chapter->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Prix -->
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label for="price" class="form-label fw-bold">
                                    <i class="bi bi-currency-euro me-2 text-primary"></i>
                                    Prix (FCFA) *
                                </label>
                                <input type="number" class="form-control" id="price" name="price" 
                                       value="{{ old('price', $subscription->price) }}" min="0" step="1" required>
                                <div class="form-text">
                                    Entrez le montant directement en FCFA
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Durée -->
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label for="duration_in_days" class="form-label fw-bold">
                                    <i class="bi bi-calendar me-2 text-primary"></i>
                                    Durée (en jours) *
                                </label>
                                <input type="number" class="form-control" id="duration_in_days" name="duration_in_days" 
                                       value="{{ old('duration_in_days', $subscription->duration_in_days) }}" min="1" max="365" required>
                                <div class="form-text">
                                    Entre 1 et 365 jours
                                </div>
                            </div>
                        </div>

                        <!-- Référence de paiement -->
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label for="payment_reference" class="form-label fw-bold">
                                    <i class="bi bi-receipt me-2 text-primary"></i>
                                    Référence de paiement
                                </label>
                                <input type="text" class="form-control" id="payment_reference" name="payment_reference" 
                                       value="{{ old('payment_reference', $subscription->payment_reference) }}" maxlength="255">
                                <div class="form-text">
                                    Optionnel, pour les paiements externes
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Validation -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_validated" name="is_validated" 
                                           value="1" {{ old('is_validated', $subscription->is_validated) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_validated">
                                        <i class="bi bi-check-circle me-2 text-success"></i>
                                        Valider la souscription
                                    </label>
                                </div>
                                <div class="form-text">
                                    Cochez cette case si la souscription est validée
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informations actuelles -->
                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="bi bi-info-circle me-2"></i>Informations actuelles
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1">
                                    <strong>Date de création :</strong> {{ $subscription->created_at->format('d/m/Y H:i') }}
                                </p>
                                <p class="mb-1">
                                    <strong>Date d'expiration :</strong> 
                                    @if($subscription->expires_at)
                                        {{ $subscription->expires_at->format('d/m/Y H:i') }}
                                    @else
                                        Non définie
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1">
                                    <strong>Statut actuel :</strong>
                                    @if($subscription->isExpired())
                                        <span class="badge bg-danger">Expirée</span>
                                    @elseif($subscription->isActive())
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-warning">En attente</span>
                                    @endif
                                </p>
                                <p class="mb-1">
                                    <strong>Jours restants :</strong> 
                                    {{ $subscription->days_remaining ?? 'N/A' }}
                                </p>
                            </div>
                        </div>
                        <div class="alert alert-warning mt-3">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Attention :</strong> La modification de la durée recalculera la date d'expiration à partir de la date de création.
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('subscriptions.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-2"></i>Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Mettre à jour la souscription
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function updateContentSelection() {
            const type = document.getElementById('type').value;
            
            // Masquer tous les groupes
            document.getElementById('formation-group').style.display = 'none';
            document.getElementById('course-group').style.display = 'none';
            document.getElementById('chapter-group').style.display = 'none';
            
            // Afficher le groupe correspondant
            if (type === 'formation') {
                document.getElementById('formation-group').style.display = 'block';
                document.getElementById('formation_id').required = true;
                document.getElementById('course_id').required = false;
                document.getElementById('chapter_id').required = false;
            } else if (type === 'course') {
                document.getElementById('course-group').style.display = 'block';
                document.getElementById('formation_id').required = false;
                document.getElementById('course_id').required = true;
                document.getElementById('chapter_id').required = false;
            } else if (type === 'chapter') {
                document.getElementById('chapter-group').style.display = 'block';
                document.getElementById('formation_id').required = false;
                document.getElementById('course_id').required = false;
                document.getElementById('chapter_id').required = true;
            }
        }

        // Initialiser au chargement
        document.addEventListener('DOMContentLoaded', function() {
            updateContentSelection();
        });
    </script>
@endsection
