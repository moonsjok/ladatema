@extends('emails.layout')

@section('title', $appNotification->title)

@section('content')
    <h2>Message personnalisé de l'administration 📬</h2>

    <p>Bonjour {{ $recipient->prenoms ?? $recipient->name }},</p>

    <p>Vous avez reçu un nouveau message de la part de l'administration de <strong>{{ config('app.name') }}</strong> :</p>

    <div class="info-box" style="border-left: 4px solid {{ $appNotification->is_important ? '#dc3545' : '#0d6efd' }}; background-color: #f8f9fa;">
        <h4 style="margin-top: 0; color: {{ $appNotification->is_important ? '#dc3545' : '#0d6efd' }};">
            @if($appNotification->is_important)
                🚨 IMPORTANT : {{ $appNotification->title }}
            @else
                📌 {{ $appNotification->title }}
            @endif
        </h4>
        <div style="font-size: 15px; line-height: 1.6; color: #333;">
            {!! nl2br(e($appNotification->message)) !!}
        </div>
    </div>

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ route('dashboard') }}" class="btn-primary">Consulter mon tableau de bord</a>
    </div>

    <p>Cordialement,</p>
    <p>— L'équipe {{ config('app.name') }}</p>
@endsection
