@extends('layouts.guest.index')

@section('title', 'Vérifier votre adresse email')

@section('content')
    <div class="d-flex justify-content-center align-items-center vh-100 bg-light">
        <div class="card shadow-sm w-100" style="max-width: 600px;">
            <div class="card-header text-center bg-primary text-white py-4">
                <h4 class="mt-2">Vérifiez votre adresse e-mail</h4>
            </div>
            <div class="card-body p-4">
                <p>
                    Merci de vous être inscrit. Avant de continuer, veuillez vérifier votre adresse e-mail en cliquant sur le lien que nous venons de vous envoyer. Si vous n'avez pas reçu l'email, nous pouvons vous en renvoyer un.
                </p>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary">Renvoyer l'email de vérification</button>
                </form>
            </div>
        </div>
    </div>
@endsection
