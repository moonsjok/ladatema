@extends('layouts.guest.index')

@section('page-title', 'Confirmation de la souscription')

@section('content')
    <div class="container py-5">
        <h2 class="mb-4 text-center text-primary">Confirmation de souscription</h2>

        <div class="card shadow-lg border-0">
            <div class="card-body p-4">
                <!-- Titre de la carte -->
                <h4 class="card-title text-center mb-4">
                    <i class="bi bi-check-circle-fill text-success me-2"></i> Détails de la souscription
                </h4>

                <!-- Informations de l'utilisateur -->
                <div class="mb-4">
                    <h5 class="text-secondary mb-3">
                        <i class="bi bi-person-fill me-2"></i> Vos informations
                    </h5>
                    <div class="bg-light p-3 rounded">
                        <p>
                            <i class="bi bi-person-badge-fill text-primary me-2"></i>
                            <strong>Nom complet :</strong>
                            {{ trim(($user->prenoms ?? '') . ' ' . ($user->nom ?? '')) ?: $user->name }}
                        </p>
                        <p>
                            <i class="bi bi-envelope-fill text-primary me-2"></i>
                            <strong>Email :</strong> {{ $user->email }}
                        </p>
                        @if(!empty($user->phone_call) || !empty($user->phone_whatsapp))
                            <p>
                                <i class="bi bi-telephone-fill text-primary me-2"></i>
                                <strong>Téléphone (appel) :</strong>
                                {{ $user->phone_call ?? 'Non renseigné' }}
                            </p>
                            <p>
                                <i class="bi bi-whatsapp text-primary me-2"></i>
                                <strong>WhatsApp :</strong>
                                {{ $user->phone_whatsapp ?? 'Non renseigné' }}
                            </p>
                        @else
                            <p class="text-muted">Pas de numéro de téléphone renseigné.</p>
                        @endif
                    </div>
                </div>

                <!-- Détails de l'abonnement -->
                <div class="mb-4">
                    <h5 class="text-secondary mb-3">
                        <i class="bi bi-book-fill me-2"></i> Détails de la souscription
                    </h5>
                    <div class="bg-light p-3 rounded">
                        <p><strong>Type :</strong> {{ ucfirst($type) }}</p>
                        <p><strong>Titre/Nom :</strong> {{ $item->title ?? 'Non disponible' }}</p>
                        <p>
                            @if (floatval($item->price) == 0)
                                <span class="badge bg-success"><i class="bi bi-unlock"></i> Gratuit</span>
                            @else
                                <strong>Prix :</strong>
                                {{ number_format($item->price, 0, ',', ' ') }} FCFA (XOF)
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Formulaire de confirmation -->

                @if ($item->price > 0)
                    <form action="{{ route('payment.process') }}" method="POST">
                        @csrf
                        <input type="hidden" name="subscription_type" value="{{ $type }}">
                        <input type="hidden" name="subscription_typeid" value="{{ $item->id }}">
                            <input type="hidden" name="prenoms" value="{{ $user->prenoms ?? '' }}">
                            <input type="hidden" name="nom" value="{{ $user->nom ?? '' }}">
                            <input type="hidden" name="email" value="{{ $user->email }}">
                            <input type="hidden" name="phone_call" value="{{ $user->phone_call ?? '' }}">
                            <input type="hidden" name="phone_whatsapp" value="{{ $user->phone_whatsapp ?? '' }}">
                        <input type="hidden" name="prenoms" value="{{ $user->prenoms ?? '' }}">
                        <input type="hidden" name="nom" value="{{ $user->nom ?? '' }}">
                        <input type="hidden" name="email" value="{{ $user->email }}" required>
                        <input type="hidden" name="phone_call" value="{{ $user->phone_call ?? '' }}">
                        <input type="hidden" name="phone_whatsapp" value="{{ $user->phone_whatsapp ?? '' }}">
                        <input type="hidden" name="amount" value="{{ number_format($item->price, 0, '', '') }}" required>
                        <input type="hidden" name="description"
                            value="Paiement pour la souscription a : {{ ucfirst($type) }} -  {{ $item->title ?? 'Non disponible' }}">
                        <div class="d-flex justify-content-between">
                            {{-- <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i> Retour
                            </a> --}}
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i> Payer pour confirmer la souscription
                            </button>
                        </div>
                    </form>

                    <h3 class="text-center">J'ai déjà payé. </h3>

                    <form method="POST" action="{{ route('subscriptions.store') }}">
                        @csrf
                        <input type="hidden" name="subscription_type" value="{{ $type }}">
                        <input type="hidden" name="subscription_typeid" value="{{ $item->id }}">
                        <input type="hidden" name="prenoms" value="{{ $user->prenoms ?? '' }}">
                        <input type="hidden" name="nom" value="{{ $user->nom ?? '' }}">
                        <input type="hidden" name="email" value="{{ $user->email }}">
                        <input type="hidden" name="phone_call" value="{{ $user->phone_call ?? '' }}">
                        <input type="hidden" name="phone_whatsapp" value="{{ $user->phone_whatsapp ?? '' }}">
                        <div class="mb-3">
                            {{-- <label for="payment_reference" class="form-label">Votre URL personnalisée</label> --}}
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon3">Référence du paiement :</span>
                                <input type="text" class="form-control @error('payment_reference') is-invalid @enderror"
                                    id="payment_reference" name="payment_reference"
                                    aria-describedby="basic-addon3 basic-addon4" value="{{ old('payment_reference') }}"
                                    .required>
                            </div>
                            @error('payment_reference')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @else
                                <div class="form-text" id="basic-addon4">
                                    Si vous avez déjà effectué le paiement, veuillez nous soumettre la référence de paiement
                                    pour la validation de votre souscription.
                                </div>
                            @enderror
                        </div>



                        <div class="d-flex justify-content-between">
                            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i> Retour
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle me-2"></i>Soumettre la référence de paiement pour validation
                            </button>
                        </div>
                    </form>
                @else
                    <div class="d-flex justify-content-between">
                        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i> Retour
                        </a>

                        <form method="POST" action="{{ route('subscriptions.store') }}">
                            @csrf
                            <input type="hidden" name="subscription_type" value="{{ $type }}">
                            <input type="hidden" name="subscription_typeid" value="{{ $item->id }}">


                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle me-2"></i> Confirmer la souscription
                            </button>
                        </form>
                    </div>
                @endif



            </div>
        </div>
    </div>
@endsection
