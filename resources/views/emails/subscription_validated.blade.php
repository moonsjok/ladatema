@extends('emails.layout')

@section('title', 'Votre souscription est validée !')

@section('content')
    <h2>Félicitations, votre accès est activé ! 🎓🚀</h2>

    <p>Bonjour {{ $user->prenoms ?? $user->name }},</p>

    <p>Nous avons le plaisir de vous informer que votre souscription sur <strong>{{ config('app.name') }}</strong> a été validée avec succès.</p>

    <div class="info-box">
        <h4 style="margin-top: 0; color: #198754;">Détails de votre accès :</h4>
        <table class="table-summary">
            <tr>
                <th>Contenu débloqué :</th>
                <td><strong>{{ $itemTitle }}</strong></td>
            </tr>
            <tr>
                <th>Type d'accès :</th>
                <td><span class="badge">{{ ucfirst($subscription->type) }}</span></td>
            </tr>
            @if($subscription->expires_at)
            <tr>
                <th>Date d'expiration :</th>
                <td><strong>{{ \Carbon\Carbon::parse($subscription->expires_at)->format('d/m/Y H:i') }}</strong></td>
            </tr>
            @endif
        </table>
    </div>

    <p>Vous pouvez dès à présent démarrer votre apprentissage et suivre vos leçons en ligne :</p>

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ $courseUrl }}" class="btn-primary" style="background-color: #198754;">Accéder à mes cours maintenant</a>
    </div>

    <p>Nous vous souhaitons une excellente progression !</p>
    <p>— L'équipe {{ config('app.name') }}</p>
@endsection
