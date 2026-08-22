@extends('emails.layout')

@section('title', 'Confirmation de votre souscription')

@section('content')
    <h2>Accusé de réception de souscription 🧾</h2>

    <p>Bonjour {{ $user->prenoms ?? $user->name }},</p>

    <p>Nous avons bien enregistré votre demande de souscription sur <strong>{{ config('app.name') }}</strong>.</p>

    <div class="info-box">
        <h4 style="margin-top: 0; color: #0d6efd;">Détails de votre souscription :</h4>
        <table class="table-summary">
            <tr>
                <th>Type de souscription :</th>
                <td><span class="badge">{{ ucfirst($subscription->type) }}</span></td>
            </tr>
            <tr>
                <th>Élément souscrit :</th>
                <td><strong>{{ $itemTitle }}</strong></td>
            </tr>
            <tr>
                <th>Montant :</th>
                <td><strong>{{ number_format($subscription->price, 0, ',', ' ') }} FCFA</strong></td>
            </tr>
            @if($subscription->payment_reference)
            <tr>
                <th>Référence paiement :</th>
                <td><code>{{ $subscription->payment_reference }}</code></td>
            </tr>
            @endif
            <tr>
                <th>Statut :</th>
                <td>
                    @if($subscription->is_validated)
                        <span style="color: #198754; font-weight: bold;">Validé</span>
                    @else
                        <span style="color: #ffc107; font-weight: bold;">En attente de validation / paiement</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    @if($subscription->is_validated)
        <p>Votre souscription est active ! Vous pouvez accéder à votre contenu dès maintenant :</p>
        <div style="text-align: center; margin: 25px 0;">
            <a href="{{ route('dashboard') }}" class="btn-primary">Accéder à mon espace de cours</a>
        </div>
    @else
        <p>Notre équipe valide votre paiement et activera vos accès sous peu. Vous recevrez une notification par e-mail dès la validation.</p>
    @endif

    <p>Merci de votre confiance !</p>
    <p>— L'équipe {{ config('app.name') }}</p>
@endsection
