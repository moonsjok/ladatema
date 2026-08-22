@extends('emails.layout')

@section('title', 'Vérification de votre adresse e-mail')

@section('content')
    <h2>Vérifiez votre adresse e-mail ✉️</h2>

    <p>Bonjour {{ $user->prenoms ?? $user->name }},</p>

    <p>Merci de vous être inscrit(e) sur <strong>{{ config('app.name') }}</strong>. Veuillez cliquer sur le bouton ci-dessous pour confirmer et activer votre adresse e-mail :</p>

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ $verificationUrl }}" class="btn-primary">Vérifier mon adresse e-mail</a>
    </div>

    <div class="info-box">
        <p style="margin: 0; font-size: 13px; color: #666;">
            Si le bouton ne fonctionne pas, copiez et collez le lien suivant dans votre navigateur :<br>
            <a href="{{ $verificationUrl }}" style="color: #0d6efd; word-break: break-all;">{{ $verificationUrl }}</a>
        </p>
    </div>

    <p style="font-size: 13px; color: #888;">Si vous n'avez pas créé de compte sur {{ config('app.name') }}, aucune action supplémentaire n'est requise.</p>
@endsection
