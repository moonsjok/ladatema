@php
    $user = Auth::user();
    $layout = 'layouts.guest.index';
    if ($user) {
        if (in_array($user->role, ['owner', 'dev', 'employee'])) {
            $layout = 'layouts.authenticated.owners.index';
        } else {
            $layout = 'layouts.authenticated.students.index';
        }
    }
@endphp

@extends($layout)

@section('title', 'Complétez votre profil')
@section('page-title', 'Complétez votre profil')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-primary text-white py-3 rounded-top-4">
                        <h4 class="mb-0 fs-5"><i class="bi bi-person-lines-fill me-2"></i>Informations de votre profil</h4>
                    </div>

                    <div class="card-body p-4">
                        @if (session('info'))
                            <div class="alert alert-warning d-flex align-items-center rounded-3">
                                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                                <span>{{ session('info') }}</span>
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="alert alert-success d-flex align-items-center rounded-3">
                                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                                <span>{{ session('success') }}</span>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('profile.complete.submit') }}">
                            @csrf

                            <div class="row g-3">
                                <div class="col-md-6 mb-3">
                                    <label for="nom" class="form-label fw-medium">
                                        <i class="bi bi-person-fill me-1 text-primary"></i>Nom <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control rounded-3 @error('nom') is-invalid @enderror" id="nom"
                                        name="nom" value="{{ old('nom', $user->nom ?? '') }}" required placeholder="Votre nom de famille">
                                    @error('nom')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="prenoms" class="form-label fw-medium">
                                        <i class="bi bi-person-vcard-fill me-1 text-primary"></i>Prénoms <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control rounded-3 @error('prenoms') is-invalid @enderror"
                                        id="prenoms" name="prenoms" value="{{ old('prenoms', $user->prenoms ?? '') }}" required placeholder="Vos prénoms">
                                    @error('prenoms')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label fw-medium">
                                    <i class="bi bi-envelope-fill me-1 text-primary"></i>Adresse E-mail <span class="text-danger">*</span>
                                </label>
                                <input type="email" class="form-control rounded-3 @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email', $user->email ?? '') }}" required placeholder="nom@exemple.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted" style="font-size: 0.8rem;">
                                    Si vous modifiez votre e-mail, un nouveau lien de vérification vous sera envoyé.
                                </small>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6 mb-3">
                                    <label for="phone_call" class="form-label fw-medium">
                                        <i class="bi bi-telephone-fill me-1 text-primary"></i>Téléphone Appels <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control rounded-3 @error('phone_call') is-invalid @enderror"
                                        id="phone_call" name="phone_call"
                                        value="{{ old('phone_call', $user->phone_call ?? '') }}" required placeholder="+228 90 00 00 00">
                                    @error('phone_call')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="phone_whatsapp" class="form-label fw-medium">
                                        <i class="bi bi-whatsapp me-1 text-success"></i>Téléphone WhatsApp <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control rounded-3 @error('phone_whatsapp') is-invalid @enderror"
                                        id="phone_whatsapp" name="phone_whatsapp"
                                        value="{{ old('phone_whatsapp', $user->phone_whatsapp ?? '') }}" required placeholder="+228 90 00 00 00">
                                    @error('phone_whatsapp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="same_phone_checkbox">
                                <label class="form-check-label text-muted small" for="same_phone_checkbox">
                                    Le numéro WhatsApp est identique au numéro d'appel
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill fw-semibold shadow-sm">
                                <i class="bi bi-check-circle-fill me-2"></i>Enregistrer et continuer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const phoneCallInput = document.getElementById('phone_call');
            const phoneWhatsappInput = document.getElementById('phone_whatsapp');
            const samePhoneCheckbox = document.getElementById('same_phone_checkbox');

            if (phoneCallInput && phoneWhatsappInput && samePhoneCheckbox) {
                samePhoneCheckbox.addEventListener('change', function () {
                    if (this.checked) {
                        phoneWhatsappInput.value = phoneCallInput.value;
                    }
                });

                phoneCallInput.addEventListener('input', function () {
                    if (samePhoneCheckbox.checked) {
                        phoneWhatsappInput.value = this.value;
                    }
                });
            }
        });
    </script>
@endsection

@section('dashboard-content')
    @yield('content')
@endsection
