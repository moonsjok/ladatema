@extends('layouts.guest.index')

@section('title', __('Forgot Password'))

@section('content')
    <div class="d-flex justify-content-center align-items-center vh-100 bg-light">
        <div class="card shadow-sm w-100" style="max-width: 400px;">
            <!-- En-tête de la carte avec icône -->
            <div class="card-header text-center bg-primary text-white py-4">
                <h4 class="mt-2">{{ __('Request Password Reset') }}</h4>
            </div>

            <!-- Corps de la carte -->
            <div class="card-body p-4">
                <form method="POST" action="{{ route('password.email') }}">
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

                    <!-- Bouton d'envoi du lien de réinitialisation -->
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="bi bi-send-fill me-2"></i>{{ __('Send Password Reset Link') }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Pied de la carte avec lien de connexion -->
            <div class="card-footer text-center py-3">
                <p class="mb-0">
                    {{ __('Remember your password?') }}<br>
                    <a href="{{ route('login') }}" class="text-decoration-none fw-bold">
                        <i class="bi bi-box-arrow-in-right me-1"></i>{{ __('Login here') }}
                    </a>
                </p>
            </div>
        </div>
    </div>
@endsection
