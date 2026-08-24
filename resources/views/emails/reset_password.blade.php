@extends('emails.layout')

@section('title', 'Réinitialisation de votre mot de passe')

@section('content')
    <h2>Réinitialisation de mot de passe 🔑</h2>

    <p>Bonjour {{ $user->prenoms ?? $user->name }},</p>

    <p>Vous recevez cet e-mail car nous avons reçu une demande de réinitialisation du mot de passe de votre compte <strong>{{ config('app.name') }}</strong>.</p>

    <div style="text-align: center; margin: 32px 0;">
        <a href="{{ $resetUrl }}" class="btn-primary">Réinitialiser mon mot de passe</a>
    </div>

    <div class="info-box">
        <p style="margin: 0; font-size: 13px; color: #495057;">
            ⏱️ Ce lien de réinitialisation expirera dans <strong>{{ $count ?? 60 }} minutes</strong>.<br>
            Si le bouton ci-dessus ne fonctionne pas, copiez et collez le lien suivant dans la barre d'adresse de votre navigateur :<br>
            <a href="{{ $resetUrl }}" style="color: #0d6efd; word-break: break-all;">{{ $resetUrl }}</a>
        </p>
    </div>

    <div class="warning-box">
        <p style="margin: 0; font-size: 13px; color: #856404;">
            ⚠️ Si vous n'avez pas demandé de réinitialisation de mot de passe, aucune action supplémentaire n'est requise. Votre mot de passe reste sécurisé.
        </p>
    </div>

    <p style="margin-top: 25px;">Cordialement,<br>— L'équipe <strong>{{ config('app.name') }}</strong></p>
@endsection
