@extends('layouts.guest.index')

@section('title', 'Vérification de l\'adresse e-mail')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">

            @php
                $user = Auth::user();
                $profilIncomplet =
                    empty($user->nom) ||
                    empty($user->prenoms) ||
                    empty($user->phone_call) ||
                    empty($user->phone_whatsapp);
            @endphp

            {{-- 🔹 Formulaire de complétion de profil --}}
            @if ($profilIncomplet)
                <div class="col-md-6">
                    <div class="card shadow border border-primary mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-person-lines-fill me-2"></i>Complétez votre profil
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted text-center">
                                Veuillez compléter votre profil avant de pouvoir valider votre adresse e-mail.
                            </p>

                            @if (session('success'))
                                <div class="alert alert-success d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill me-2"></i>
                                    <span>{{ session('success') }}</span>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('verification.completeprofile') }}"
                                class="needs-validation" novalidate>
                                @csrf

                                {{-- Nom --}}
                                <div class="mb-3">
                                    <label for="nom" class="form-label">
                                        <i class="bi bi-person-fill me-1"></i>Nom
                                    </label>
                                    <input type="text" id="nom" name="nom"
                                        class="form-control @error('nom') is-invalid @enderror"
                                        value="{{ old('nom', $user->nom) }}" required>
                                    @error('nom')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Prénoms --}}
                                <div class="mb-3">
                                    <label for="prenoms" class="form-label">
                                        <i class="bi bi-person-vcard-fill me-1"></i>Prénoms
                                    </label>
                                    <input type="text" id="prenoms" name="prenoms"
                                        class="form-control @error('prenoms') is-invalid @enderror"
                                        value="{{ old('prenoms', $user->prenoms) }}" required>
                                    @error('prenoms')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Téléphone appels --}}
                                <div class="mb-3">
                                    <label for="phone_call" class="form-label">
                                        <i class="bi bi-telephone-fill me-1"></i>Téléphone (Appels)
                                    </label>
                                    <input type="text" id="phone_call" name="phone_call"
                                        class="form-control @error('phone_call') is-invalid @enderror"
                                        value="{{ old('phone_call', $user->phone_call) }}" required>
                                    @error('phone_call')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Téléphone WhatsApp --}}
                                <div class="mb-3">
                                    <label for="phone_whatsapp" class="form-label">
                                        <i class="bi bi-whatsapp me-1"></i>Téléphone WhatsApp
                                    </label>
                                    <input type="text" id="phone_whatsapp" name="phone_whatsapp"
                                        class="form-control @error('phone_whatsapp') is-invalid @enderror"
                                        value="{{ old('phone_whatsapp', $user->phone_whatsapp) }}" required>
                                    @error('phone_whatsapp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Adresse e-mail (modifiable) --}}
                                <div class="mb-3">
                                    <label for="email" class="form-label">
                                        <i class="bi bi-envelope-fill me-1"></i>Adresse e-mail
                                    </label>
                                    <input type="email" id="email" name="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Si votre adresse e-mail est incorrecte, vous pouvez la corriger ici.<br>
                                        <strong>Cette adresse sera utilisée pour vous connecter.</strong>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-save me-2"></i>Enregistrer et continuer
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            {{-- 🔹 Formulaire de renvoi de l'e-mail de validation --}}
            @if (!$profilIncomplet)
                <div class="col-md-6">
                    <div class="card shadow border border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-envelope-check-fill me-2"></i>Vérifiez votre adresse e-mail
                            </h5>
                        </div>
                        <div class="card-body">
                            <p>
                                Un lien de vérification a été envoyé à :
                                <strong>{{ $user->email }}</strong>.
                                Cliquez sur ce lien pour activer votre compte.
                            </p>

                            <p class="small text-muted">
                                Vous pouvez modifier votre adresse ici avant de renvoyer l’e-mail.<br>
                                <strong>C’est cette adresse qui sera utilisée pour vous connecter.</strong>
                            </p>

                            <form method="POST" action="{{ route('verification.send') }}" class="needs-validation"
                                novalidate>
                                @csrf

                                <div class="mb-3">
                                    <label for="email" class="form-label">
                                        <i class="bi bi-envelope-at-fill me-1"></i>Adresse e-mail
                                    </label>
                                    <input type="email" id="email" name="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-send-fill me-2"></i>Renvoyer l’e-mail de vérification
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Validation Bootstrap 5.3 côté client
        (() => {
            'use strict';
            const forms = document.querySelectorAll('.needs-validation');
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
@endpush
