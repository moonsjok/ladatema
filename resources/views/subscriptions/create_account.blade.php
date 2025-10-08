@extends('layouts.guest.index')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <!-- Titre avec icône -->
                <h1 class="text-center mb-4">
                    <i class="bi bi-person-plus-fill me-2"></i>Créer un compte pour continuer
                </h1>

                <!-- Formulaire -->
                <form method="POST" action="{{ route('subscriptions.storeAccount') }}" class="needs-validation" novalidate>
                    @csrf
                    <input type="hidden" name="type" value="{{ old('type', $request->type) }}">
                    <input type="hidden" name="typeid" value="{{ old('typeid', $request->typeid) }}">

                    <!-- Prénoms et Nom -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="prenoms" class="form-label fw-bold">
                                <i class="bi bi-person-fill me-1"></i>Prénoms <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('prenoms') is-invalid @enderror" id="prenoms"
                                name="prenoms" value="{{ old('prenoms') }}" required>
                            @error('prenoms')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @else
                                <div class="form-text">Entrez vos prénoms.</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="nom" class="form-label fw-bold">
                                <i class="bi bi-person-badge-fill me-1"></i>Nom <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom"
                                name="nom" value="{{ old('nom') }}" required>
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @else
                                <div class="form-text">Entrez votre nom de famille.</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold">
                            <i class="bi bi-envelope-fill me-1"></i>Email <span class="text-danger">*</span>
                        </label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                            name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @else
                            <div class="form-text">Votre email servira d'identifiant de connexion.</div>
                        @enderror
                    </div>

                    <!-- Téléphone (appel) -->
                    <div class="mb-3">
                        <label for="phone_call" class="form-label fw-bold">
                            <i class="bi bi-telephone-fill me-1"></i>Numéro (appel) <span class="text-danger">*</span>
                        </label>
                        <input type="tel" class="form-control @error('phone_call') is-invalid @enderror" id="phone_call"
                            name="phone_call" value="{{ old('phone_call') }}" placeholder="+228 90 00 00 00" required>
                        @error('phone_call')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @else
                            <div class="form-text">Numéro principal pour vous contacter par appel.</div>
                        @enderror
                    </div>

                    <!-- Téléphone (WhatsApp) -->
                    <div class="mb-3">
                        <label for="phone_whatsapp" class="form-label fw-bold">
                            <i class="bi bi-whatsapp me-1"></i>Numéro (WhatsApp) <span class="text-danger">*</span>
                        </label>
                        <input type="tel" class="form-control @error('phone_whatsapp') is-invalid @enderror"
                            id="phone_whatsapp" name="phone_whatsapp" value="{{ old('phone_whatsapp') }}"
                            placeholder="+228 90 00 00 00" required>
                        @error('phone_whatsapp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @else
                            <div class="form-text">Numéro WhatsApp pour les notifications et support.</div>
                        @enderror
                    </div>

                    <!-- Mot de passe -->
                    <div class="mb-3">
                        <label for="password" class="form-label fw-bold">
                            <i class="bi bi-lock-fill me-1"></i>Mot de passe <span class="text-danger">*</span>
                        </label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                            name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @else
                            <div class="form-text">Utilisez au moins 8 caractères, incluant des chiffres et des lettres.</div>
                        @enderror
                    </div>

                    <!-- Confirmation du mot de passe -->
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label fw-bold">
                            <i class="bi bi-lock-fill me-1"></i>Confirmer le mot de passe <span class="text-danger">*</span>
                        </label>
                        <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                            id="password_confirmation" name="password_confirmation" required>
                        @error('password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @else
                            <div class="form-text">Répétez le mot de passe pour confirmation.</div>
                        @enderror
                    </div>

                    <!-- Bouton de soumission -->
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-circle-fill me-2"></i>Créer un compte
                        </button>
                    </div>
                </form>

                <!-- Lien pour les utilisateurs déjà inscrits -->
                <div class="text-center mt-4">
                    <p class="text-muted">Vous avez déjà un compte ?</p>
                    <a href="{{ route('login') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Se connecter
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Script pour la validation Bootstrap -->
    <script>
        (function() {
            'use strict'
            const forms = document.querySelectorAll('.needs-validation')
            Array.from(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()
    </script>
@endsection
