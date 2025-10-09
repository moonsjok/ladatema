@extends('layouts.guest.index')

@section('title', 'Complétez votre profil')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4><i class="bi bi-person-lines-fill me-2"></i>Complétez votre profil</h4>
                    </div>

                    <div class="card-body">
                        @if (session('info'))
                            <div class="alert alert-warning d-flex align-items-center">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <span>{{ session('info') }}</span>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('profile.complete.submit') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="nom" class="form-label">
                                    <i class="bi bi-person-fill me-1"></i>Nom
                                </label>
                                <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom"
                                    name="nom" value="{{ old('nom', Auth::user()->nom) }}">
                                @error('nom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="prenoms" class="form-label">
                                    <i class="bi bi-person-vcard-fill me-1"></i>Prénoms
                                </label>
                                <input type="text" class="form-control @error('prenoms') is-invalid @enderror"
                                    id="prenoms" name="prenoms" value="{{ old('prenoms', Auth::user()->prenoms) }}">
                                @error('prenoms')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="phone_call" class="form-label">
                                    <i class="bi bi-telephone-fill me-1"></i>Téléphone (Appels)
                                </label>
                                <input type="text" class="form-control @error('phone_call') is-invalid @enderror"
                                    id="phone_call" name="phone_call"
                                    value="{{ old('phone_call', Auth::user()->phone_call) }}">
                                @error('phone_call')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="phone_whatsapp" class="form-label">
                                    <i class="bi bi-whatsapp me-1"></i>Téléphone WhatsApp
                                </label>
                                <input type="text" class="form-control @error('phone_whatsapp') is-invalid @enderror"
                                    id="phone_whatsapp" name="phone_whatsapp"
                                    value="{{ old('phone_whatsapp', Auth::user()->phone_whatsapp) }}">
                                @error('phone_whatsapp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-save me-2"></i>Enregistrer et continuer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
