@extends('layouts.guest.index')

@section('title', __('Reset Password'))

@section('content')
    <div class="d-flex justify-content-center align-items-center vh-100 bg-light">
        <div class="card shadow-sm w-100" style="max-width: 400px;">
            <!-- En-tête de la carte avec icône -->
            <div class="card-header text-center bg-primary text-white py-4">
                <i class="bi bi-key-fill fs-1"></i>
                <h4 class="mt-2">{{ __('Reset Password') }}</h4>
            </div>

            <!-- Corps de la carte -->
            <div class="card-body p-4">
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf

                    <!-- Champ caché pour le token -->
                    <input type="hidden" name="token" value="{{ $token }}">

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

                    <!-- Champ Nouveau mot de passe -->
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
                        <label for="password_confirmation" class="form-label">
                            <i class="bi bi-lock-fill me-1"></i>{{ __('Confirm Password') }}
                        </label>
                        <input id="password_confirmation" type="password" class="form-control" name="password_confirmation"
                            required autocomplete="new-password">
                    </div>

                    <!-- Bouton de réinitialisation -->
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="bi bi-arrow-repeat me-2"></i>{{ __('Reset Password') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
