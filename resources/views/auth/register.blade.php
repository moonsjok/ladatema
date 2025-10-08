@extends('layouts.guest.index')

@section('title', 'Register')

@section('content')
    <div class=" mt-5 mb-5 d-flex justify-content-center align-items-center vh-100 bg-light">
        <div class="card shadow-sm w-100" style="max-width: 400px;">
            <!-- En-tête de la carte avec icône -->
            <div class="card-header text-center bg-primary text-white py-4">
                <h4 class="mt-2 display-6">{{ __('Sign up') }}</h4>
            </div>

            <!-- Corps de la carte -->
            <div class="card-body p-4">
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <!-- Champ Prénoms et Nom -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="prenoms" class="form-label">
                                <i class="bi bi-person-fill me-1"></i>Prénoms
                            </label>
                            <input id="prenoms" type="text" class="form-control @error('prenoms') is-invalid @enderror"
                                name="prenoms" value="{{ old('prenoms') }}" required autocomplete="given-name" autofocus>
                            @error('prenoms')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="nom" class="form-label">
                                <i class="bi bi-person-badge-fill me-1"></i>Nom
                            </label>
                            <input id="nom" type="text" class="form-control @error('nom') is-invalid @enderror"
                                name="nom" value="{{ old('nom') }}" required autocomplete="family-name">
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Champ Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="bi bi-envelope-fill me-1"></i>{{ __('Email Address') }}
                        </label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                            name="email" value="{{ old('email') }}" required autocomplete="email">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Champ Téléphone (Appel) -->
                    <div class="mb-3">
                        <label for="phone_call" class="form-label">
                            <i class="bi bi-telephone-fill me-1"></i>Numéro de téléphone (Appel)
                        </label>
                        <input id="phone_call" type="tel" inputmode="tel" pattern="[0-9+()\s-]*" maxlength="30"
                            class="form-control @error('phone_call') is-invalid @enderror" name="phone_call"
                            value="{{ old('phone_call') }}" autocomplete="tel">
                        @error('phone_call')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Champ Téléphone (WhatsApp) -->
                    <div class="mb-3">
                        <label for="phone_whatsapp" class="form-label">
                            <i class="bi bi-whatsapp me-1"></i>Numéro de téléphone (WhatsApp)
                        </label>
                        <input id="phone_whatsapp" type="tel" inputmode="tel" pattern="[0-9+()\s-]*" maxlength="30"
                            class="form-control @error('phone_whatsapp') is-invalid @enderror" name="phone_whatsapp"
                            value="{{ old('phone_whatsapp') }}" autocomplete="tel">
                        @error('phone_whatsapp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Champ Mot de passe -->
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="bi bi-lock-fill me-1"></i>{{ __('Password') }}
                        </label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                            name="password" required autocomplete="new-password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Champ Confirmation du mot de passe -->
                    <div class="mb-3">
                        <label for="password-confirm" class="form-label">
                            <i class="bi bi-lock-fill me-1"></i>{{ __('Confirm Password') }}
                        </label>
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation"
                            required autocomplete="new-password">
                    </div>

                    <!-- Bouton d'inscription -->
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="bi bi-person-plus-fill me-2"></i>{{ __('Register') }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Pied de la carte avec lien de connexion -->
            <div class="card-footer text-center py-3">
                <p class="mb-0">
                    {{ __('Already have an account?') }}
                    <a href="{{ route('login') }}" class="text-decoration-none fw-bold">
                        <i class="bi bi-box-arrow-in-right me-1"></i>{{ __('Se connecter') }}
                    </a>
                </p>
            </div>
        </div>
    </div>
@endsection
