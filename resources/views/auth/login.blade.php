@extends('layouts.guest.index')

@section('title', __('Login'))

@section('content')
    <div class="d-flex justify-content-center align-items-center vh-100 bg-light">
        <div class="card shadow-sm w-100" style="max-width: 400px;">
            <!-- En-tête de la carte avec icône -->
            <div class="card-header text-center bg-primary text-white py-4">
                <h4 class="mt-2">
                    <span class="display-6">
                        {{ __('Se connecter') }}
                    </span>
                </h4>
            </div>

            <!-- Corps de la carte -->
            <div class="card-body p-4">
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Champ Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="bi bi-envelope-fill me-1"></i>{{ __('Email Address') }}
                        </label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                            name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Champ Mot de passe -->
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="bi bi-lock-fill me-1"></i>{{ __('Password') }}
                        </label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                            name="password" required autocomplete="current-password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Case à cocher "Se souvenir de moi" -->
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember"
                            {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">
                            {{ __('Remember Me') }}
                        </label>
                    </div>

                    <!-- Lien "Mot de passe oublié" -->
                    <div class="mb-3 text-end">
                        <a href="{{ route('password.request') }}" class="text-decoration-none">
                            <i class="bi bi-question-circle-fill me-1"></i>{{ __('Forgot Your Password?') }}
                        </a>
                    </div>

                    <!-- Bouton de connexion -->
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="bi bi-box-arrow-in-right me-2"></i>{{ __('Login') }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Pied de la carte avec lien d'inscription -->
            <div class="card-footer text-center py-3">
                <p class="mb-0">
                    {{ __('Don’t have an account?') }}
                    <a href="{{ route('register') }}" class="text-decoration-none fw-bold">
                        <i class="bi bi-person-plus-fill me-1"></i>{{ __("S'inscrire") }}
                    </a>
                </p>
            </div>
        </div>
    </div>
@endsection
