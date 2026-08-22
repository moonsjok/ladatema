@extends('emails.layout')

@section('title', 'Bienvenue sur ' . config('app.name'))

@section('content')
    <h2>Bienvenue sur {{ config('app.name') }}, {{ $user->prenoms ?? $user->name }} ! 🎉</h2>

    <p>Nous sommes ravis de vous compter parmi les membres de notre communauté d'apprentissage en ligne.</p>

    <div class="info-box">
        <p style="margin: 0;"><strong>Votre compte a été créé avec succès.</strong></p>
        <p style="margin: 5px 0 0 0; font-size: 14px; color: #555;">Adresse e-mail : <strong>{{ $user->email }}</strong></p>
    </div>

    <p>Pour pouvoir profiter pleinement de l'ensemble de nos formations et cours interactifs, assurez-vous que votre adresse e-mail est bien vérifiée et votre profil complété.</p>

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ route('guest.formationsList') }}" class="btn-primary">Découvrir le Catalogue des Formations</a>
    </div>

    <p>Si vous avez la moindre question ou besoin d'assistance pour choisir votre parcours, notre équipe pédagogique est à votre entière disposition.</p>

    <p>Excellente formation sur {{ config('app.name') }} !</p>
    <p>— L'équipe {{ config('app.name') }}</p>
@endsection
