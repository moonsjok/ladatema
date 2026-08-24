@extends('emails.layout')

@section('title', 'Vérification de votre adresse e-mail')

@section('content')
    <h2>Vérification de votre e-mail ✉️</h2>

    <p>Bonjour {{ $user->prenoms ?? $user->name }},</p>

    <p>Bienvenue sur <strong>{{ config('app.name') }}</strong> ! Merci d'avoir créé votre compte. Veuillez cliquer sur le bouton ci-dessous pour confirmer et activer votre adresse e-mail :</p>

    <div style="text-align: center; margin: 32px 0;">
        <a href="{{ $verificationUrl }}" class="btn-primary">Vérifier mon adresse e-mail</a>
    </div>

    <div class="info-box">
        <p style="margin: 0; font-size: 13px; color: #495057;">
            Si le bouton ci-dessus ne fonctionne pas, copiez et collez le lien suivant dans la barre d'adresse de votre navigateur :<br>
            <a href="{{ $verificationUrl }}" style="color: #0d6efd; word-break: break-all;">{{ $verificationUrl }}</a>
        </p>
    </div>

    <p style="font-size: 13px; color: #6c757d; margin-top: 25px;">
        Si vous n'avez pas créé de compte sur {{ config('app.name') }}, aucune action supplémentaire n'est requise.
    </p>

    <p style="margin-top: 20px;">Cordialement,<br>— L'équipe <strong>{{ config('app.name') }}</strong></p>
@endsection
