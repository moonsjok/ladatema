@extends('layouts.guest.index')
@section('page-title', 'Nos Services')
@section('content')
    <div class="container-fluid mt-5">
        <h1 class="text-center mb-4 bg-white">Nos services</h1>

        <!-- Services Sections -->
        <div class="row align-items-center mb-5 bg-white p-5 rounded">
            <div class="col-md-6 text-center">
                <i class="bi bi-calculator-fill text-primary display-1"></i>
            </div>
            <div class="col-md-6">
                <h2>Assistance Comptable et Fiscale</h2>
                <p>Nous vous accompagnons dans la gestion quotidienne de votre comptabilité et dans l’optimisation de votre
                    fiscalité.</p>
                <ul class="list-group list-group-flush  bg-light">
                    <li class="list-group-item  bg-light">
                        <i class="bi bi-check-circle-fill text-success"></i>
                        Tenue et suivi comptable.
                    </li>
                    <li class="list-group-item  bg-light">
                        <i class="bi bi-check-circle-fill text-success"></i>
                        Déclarations fiscales en conformité.
                    </li>
                    <li class="list-group-item  bg-light">
                        <i class="bi bi-check-circle-fill text-success"></i>
                        Conseil fiscal pour optimiser votre entreprise.
                    </li>
                </ul>
            </div>
        </div>

        <div class="row align-items-center mb-5 flex-md-row-reverse bg-light p-5 rounded">
            <div class="col-md-6 text-center">
                <i class="bi bi-clipboard-check-fill text-primary display-1"></i>
            </div>
            <div class="col-md-6">
                <h2>Audit et Commissariat aux Comptes</h2>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <i class="bi bi-check-circle-fill text-success"></i>
                        Audit financier et certification.
                    </li>
                    <li class="list-group-item">
                        <i class="bi bi-check-circle-fill text-success"></i>
                        Audit interne et contrôle des risques.
                    </li>
                    <li class="list-group-item">
                        <i class="bi bi-check-circle-fill text-success"></i>
                        Commissariat aux comptes.
                    </li>
                </ul>
            </div>
        </div>

        <div class="row align-items-center mb-5 bg-white p-5 rounded">
            <div class="col-md-6 text-center">
                <i class="bi bi-building-fill text-primary display-1"></i>
            </div>
            <div class="col-md-6">
                <h2>Conseil en Création et Acquisition d’Entreprise</h2>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item  bg-light">
                        <i class="bi bi-check-circle-fill text-success"></i>
                        création d’entreprise.
                    </li>
                    <li class="list-group-item  bg-light">
                        <i class="bi bi-check-circle-fill text-success"></i>
                        Évaluation et reprise d’entreprise.
                    </li>
                    <li class="list-group-item  bg-light">
                        <i class="bi bi-check-circle-fill text-success"></i>
                        Optimisation de la gestion financière.
                    </li>
                </ul>
            </div>
        </div>

        <div class="row align-items-center mb-5 flex-md-row-reverse bg-light p-5 rounded">
            <div class="col-md-6 text-center">
                <i class="bi bi-people-fill text-primary display-1"></i>
            </div>
            <div class="col-md-6">
                <h2>Représentation et Assistance Commerciale</h2>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <i class="bi bi-check-circle-fill text-success"></i>
                        Représentation fiscale
                        et légale.
                    </li>
                    <li class="list-group-item">
                        <i class="bi bi-check-circle-fill text-success"></i>
                        Développement commercial et prospection.
                    </li>
                    <li class="list-group-item"><i class="bi bi-check-circle-fill text-success"></i>
                        Négociation et suivi des contrats.
                    </li>
                </ul>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="text-center mt-5">
            <h3 class="mb-3">Prêt à transformer votre entreprise ?</h3>
            <p>Contactez-nous dès aujourd'hui pour mettre votre entreprise sur la voie de l'excellence.</p>
            <a href="contact.html" class="btn btn-primary btn-lg"><i class="bi bi-envelope-fill"></i> Contactez-nous</a>
        </div>
    </div>
@endsection
