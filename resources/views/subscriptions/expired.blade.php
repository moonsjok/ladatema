@extends('layouts.authenticated.students.index')

@section('page-title', 'Souscription Expirée')

@section('dashboard-content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-danger text-white text-center py-4">
                    <div class="mb-2">
                        <i class="bi bi-clock-history display-3"></i>
                    </div>
                    <h3 class="fw-bold mb-0">Souscription Expirée</h3>
                    <p class="mb-0 text-white-50">L'accès à ce contenu est actuellement suspendu</p>
                </div>

                <div class="card-body p-4 p-md-5">
                    <div class="alert alert-warning border-start border-warning border-4 mb-4">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-exclamation-triangle-fill fs-3 text-warning me-3"></i>
                            <div>
                                <strong>Attention :</strong> La date limite d'accès pour ce contenu est dépassée. Pour continuer votre apprentissage, veuillez demander une prolongation à l'administrateur.
                            </div>
                        </div>
                    </div>

                    <h5 class="fw-bold text-secondary mb-3">Détails de la souscription :</h5>
                    
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered align-middle">
                            <tbody>
                                <tr>
                                    <th class="bg-light w-40">Élément souscrit :</th>
                                    <td>
                                        <strong class="text-primary fs-5">
                                            @if($subscription->formation)
                                                {{ $subscription->formation->title }} (Formation)
                                            @elseif($subscription->course)
                                                {{ $subscription->course->title }} (Cours)
                                            @elseif($subscription->chapter)
                                                {{ $subscription->chapter->title }} (Chapitre)
                                            @else
                                                Contenu Pédagogique
                                            @endif
                                        </strong>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Type d'accès :</th>
                                    <td><span class="badge bg-secondary text-uppercase">{{ $subscription->type }}</span></td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Date d'expiration :</th>
                                    <td>
                                        <span class="badge bg-danger fs-6">
                                            <i class="bi bi-calendar-x me-1"></i>
                                            @if($subscription->expires_at)
                                                {{ is_string($subscription->expires_at) ? \Carbon\Carbon::parse($subscription->expires_at)->format('d/m/Y à H:i') : $subscription->expires_at->format('d/m/Y à H:i') }}
                                            @else
                                                Date d'expiration atteinte
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                                @if($subscription->payment_reference)
                                <tr>
                                    <th class="bg-light">Référence paiement :</th>
                                    <td><code>{{ $subscription->payment_reference }}</code></td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="bg-light p-4 rounded-3 text-center mb-4 border">
                        <h5 class="fw-bold mb-2">Demander une prolongation d'accès</h5>
                        <p class="text-muted small mb-3">
                            Contactez directement l'équipe d'administration Ladatema pour renouveler ou prolonger la durée de votre souscription.
                        </p>

                        @php
                            $itemTitle = $subscription->formation ? $subscription->formation->title : ($subscription->course ? $subscription->course->title : ($subscription->chapter ? $subscription->chapter->title : 'mon cours'));
                            $waMessage = rawurlencode("Bonjour Ladatema, je souhaite demander une prolongation pour ma souscription à : " . $itemTitle . " (Email: " . auth()->user()->email . ")");
                            $waUrl = "https://wa.me/22892980842?text=" . $waMessage;
                        @endphp

                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <a href="{{ $waUrl }}" target="_blank" class="btn btn-success btn-lg px-4 me-md-2 fw-semibold">
                                <i class="bi bi-whatsapp me-2"></i> Contacter sur WhatsApp
                            </a>
                            <a href="tel:+22892980842" class="btn btn-outline-primary btn-lg px-4 fw-semibold">
                                <i class="bi bi-telephone-fill me-2"></i> Appeler le Support
                            </a>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary me-2">
                            <i class="bi bi-speedometer2 me-1"></i> Retour au Tableau de bord
                        </a>
                        <a href="{{ route('guest.formationsList') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-journal-album me-1"></i> Parcourir le catalogue
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
