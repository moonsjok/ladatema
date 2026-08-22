@extends('emails.layout')

@section('title', 'Alerte : Nouvelle souscription reçue')

@section('content')
    <h2>Alerte Admin : Nouvelle souscription 🔔</h2>

    <p>Une nouvelle souscription a été enregistrée sur la plateforme <strong>{{ config('app.name') }}</strong>.</p>

    <div class="info-box">
        <h4 style="margin-top: 0; color: #0d6efd;">Informations de la souscription :</h4>
        <table class="table-summary">
            <tr>
                <th>Apprenant :</th>
                <td><strong>{{ $user->nom }} {{ $user->prenoms }}</strong> ({{ $user->email }})</td>
            </tr>
            <tr>
                <th>Téléphone :</th>
                <td>{{ $user->phone_call ?? 'Non renseigné' }} (WhatsApp: {{ $user->phone_whatsapp ?? 'Non' }})</td>
            </tr>
            <tr>
                <th>Type :</th>
                <td><span class="badge">{{ ucfirst($subscription->type) }}</span></td>
            </tr>
            <tr>
                <th>Contenu souscrit :</th>
                <td><strong>{{ $itemTitle }}</strong></td>
            </tr>
            <tr>
                <th>Prix :</th>
                <td><strong>{{ number_format($subscription->price, 0, ',', ' ') }} FCFA</strong></td>
            </tr>
            @if($subscription->payment_reference)
            <tr>
                <th>Référence paiement :</th>
                <td><code>{{ $subscription->payment_reference }}</code></td>
            </tr>
            @endif
            <tr>
                <th>Statut actuel :</th>
                <td>
                    @if($subscription->is_validated)
                        <span style="color: #198754; font-weight: bold;">Validé</span>
                    @else
                        <span style="color: #dc3545; font-weight: bold;">En attente de validation</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <div style="text-align: center; margin: 25px 0;">
        <a href="{{ route('subscriptions.index') }}" class="btn-primary">Gérer les souscriptions dans l'Admin</a>
    </div>
@endsection
