@extends('emails.layout')

@section('title', 'Mise à jour de votre souscription')

@section('content')
    <h2>Mise à jour de votre souscription 🔄</h2>

    <p>Bonjour {{ $user->prenoms ?? $user->name }},</p>

    <p>Nous vous informons que les détails de votre souscription pour <strong>{{ $itemTitle }}</strong> sur <strong>{{ config('app.name') }}</strong> ont été mis à jour par l'administration.</p>

    @if(!empty($changes))
        <div class="info-box" style="border-left: 4px solid #0d6efd; background-color: #f8f9fa;">
            <h4 style="margin-top: 0; color: #0d6efd;">Modifications apportées :</h4>
            <ul style="margin-bottom: 0; padding-left: 20px;">
                @foreach($changes as $change)
                    <li style="margin-bottom: 8px;">{!! $change !!}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="info-box">
        <h4 style="margin-top: 0; color: #333;">Nouveaux détails de votre souscription :</h4>
        <table class="table-summary">
            <tr>
                <th>Élément souscrit :</th>
                <td><strong>{{ $itemTitle }}</strong></td>
            </tr>
            <tr>
                <th>Durée de validité :</th>
                <td><strong>{{ $subscription->duration_in_days ?? 90 }} jours</strong></td>
            </tr>
            @if($subscription->expires_at)
            <tr>
                <th>Date d'expiration :</th>
                <td><strong>{{ \Carbon\Carbon::parse($subscription->expires_at)->format('d/m/Y') }}</strong></td>
            </tr>
            @endif
            <tr>
                <th>Statut :</th>
                <td>
                    @if($subscription->is_validated)
                        <span style="color: #198754; font-weight: bold;">Validé (Accès actif)</span>
                    @else
                        <span style="color: #ffc107; font-weight: bold;">En attente de validation</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ route('dashboard') }}" class="btn-primary">Consulter mon tableau de bord</a>
    </div>

    <p>Si vous avez des questions concernant cette modification, n'hésitez pas à nous contacter.</p>
    <p>— L'équipe {{ config('app.name') }}</p>
@endsection
