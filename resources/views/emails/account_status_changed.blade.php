@extends('emails.layout')

@section('title', $isActive ? 'Réactivation de votre compte' : 'Désactivation de votre compte')

@section('content')
    @if($isActive)
        <h2>Votre compte a été réactivé ! 🎉</h2>

        <p>Bonjour {{ $user->prenoms ?? $user->name }},</p>

        <p>Nous avons le plaisir de vous informer que votre compte sur la plateforme <strong>{{ config('app.name') }}</strong> a été réactivé avec succès par l'administration.</p>

        <p>Vous pouvez de nouveau vous connecter et accéder immédiatement à vos formations et cours.</p>

        <div style="text-align: center; margin: 32px 0;">
            <a href="{{ route('login') }}" class="btn-primary">Se connecter à mon compte</a>
        </div>
    @else
        <h2>Notification concernant votre compte ⚠️</h2>

        <p>Bonjour {{ $user->prenoms ?? $user->name }},</p>

        <p>Nous vous informons que votre compte sur la plateforme <strong>{{ config('app.name') }}</strong> a été temporairement désactivé par l'administration.</p>

        <div class="info-box" style="border-left-color: #dc3545; background-color: #fff8f8;">
            <p style="margin: 0; color: #842029; font-weight: 600;">
                Accès temporairement restreint
            </p>
            <p style="margin: 6px 0 0 0; font-size: 13px; color: #58151c;">
                Toutes les tentatives de connexion avec vos identifiants seront refusées tant que le compte reste désactivé.
            </p>
        </div>

        <p style="margin-top: 20px;">Si vous estimez qu'il s'agit d'une erreur ou si vous souhaitez obtenir des informations complémentaires, veuillez contacter l'administration de Ladatema Research.</p>
    @endif

    <p style="margin-top: 25px;">Cordialement,<br>— L'équipe <strong>{{ config('app.name') }}</strong></p>
@endsection
