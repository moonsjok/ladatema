@extends('layouts.guest.index')

@section('content')
    <style>
        .container {
            max-width: 960px;
        }

        .icon-link>.bi {
            width: .75em;
            height: .75em;
        }

        /*
                                                                                                                                                                         * Custom translucent site header
                                                                                                                                                                         */

        .site-header {
            background-color: rgba(0, 0, 0, .85);
            -webkit-backdrop-filter: saturate(180%) blur(20px);
            backdrop-filter: saturate(180%) blur(20px);
        }

        .site-header a {
            color: #8e8e8e;
            transition: color .15s ease-in-out;
        }

        .site-header a:hover {
            color: #fff;
            text-decoration: none;
        }

        /*
                                                                                                                                                                         * Dummy devices (replace them with your own or something else entirely!)
                                                                                                                                                                         */

        .product-device {
            position: absolute;
            right: 10%;
            bottom: -30%;
            width: 300px;
            height: 540px;
            background-color: #333;
            border-radius: 21px;
            transform: rotate(30deg);
        }

        .product-device::before {
            position: absolute;
            top: 10%;
            right: 10px;
            bottom: 10%;
            left: 10px;
            content: "";
            background-color: rgba(255, 255, 255, .1);
            border-radius: 5px;
        }

        .product-device-2 {
            top: -25%;
            right: auto;
            bottom: 0;
            left: 5%;
            background-color: #e5e5e5;
        }


        /*
                                                                                                                                                                         * Extra utilities
                                                                                                                                                                         */

        .flex-equal>* {
            flex: 1;
        }

        @media (min-width: 768px) {
            .flex-md-equal>* {
                flex: 1;
            }
        }
    </style>
    <main>
        <div class="position-relative overflow-hidden p-3 p-md-5 m-md-3 text-center bg-body-tertiary">
            <div class="col-md-6 p-lg-5 mx-auto my-5">
                <h1 class="display-3 fw-bold text-primary">Blockchain Academy</h1>
                <h3 class="fw-normal text-muted mb-3">Développez vos compétences en blockchain avec formations.</h3>
                <div class="d-flex gap-3 justify-content-center lead fw-normal">
                    <a class="icon-link" href="{{ route('register') }}">
                        Créer votre compte
                        <svg class="bi">
                            <use xlink:href="#chevron-right" />
                        </svg>
                    </a>
                    <a class="icon-link" href="#">
                        Parcourir nos formations
                        <svg class="bi">
                            <use xlink:href="#chevron-right" />
                        </svg>
                    </a>
                </div>
            </div>
            <div class="product-device shadow-sm d-none d-md-block"></div>
            <div class="product-device product-device-2 shadow-sm d-none d-md-block"></div>
        </div>

        <div class="container-fluid my-5">
            <div class="row g-4">
                <!-- Première colonne : Description -->
                <div class="col-md-4 ">
                    <div class="p-3 border rounded shadow-sm ">
                        <h2 class="fw-bold display-5">
                            <span class="text-primary">Apprendre grâce à</span> plusieurs formats
                        </h2>
                        <p class="text-muted display-6 text-justify ">
                            Vous cherchez une formation complète pour apprendre de A à Z ou une vidéo pour découvrir un
                            nouvel outil ?
                            Vous devriez trouver votre bonheur.
                        </p>
                    </div>
                </div>

                <!-- Deuxième colonne : Formations avec Cours et Chapitres -->
                <div class="col-md-8">
                    <!-- Bloc des formations -->
                    <fieldset class="border p-3 shadow-sm mb-4">
                        <legend class="fw-bold text-muted" style="margin-top:-40px;">
                            <span class="badge bg-primary rounded">
                                <i class="bi bi-mortarboard-fill text-white me-2"></i>
                                Les dernières formations</span>
                        </legend>
                        <ul class="list-group list-group-flush">
                            @foreach ($formations as $formation)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="fw-bold mb-0">
                                            <i class="bi bi-bookmark-star text-success me-2"></i> {{ $formation->title }}
                                        </h6>
                                        <small class="text-muted">
                                            <i class="bi bi-collection-play-fill text-info me-2"></i>
                                            {{ $formation->courses->count() }} cours
                                        </small>
                                    </div>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="fw-bold mb-0">
                                            <i class="bi bi-bookmark-star text-success me-2"></i> {{ $formation->title }}
                                        </h6>
                                        <small class="text-muted">
                                            <i class="bi bi-collection-play-fill text-info me-2"></i>
                                            {{ $formation->courses->count() }} cours
                                        </small>
                                    </div>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="fw-bold mb-0">
                                            <i class="bi bi-bookmark-star text-success me-2"></i> {{ $formation->title }}
                                        </h6>
                                        <small class="text-muted">
                                            <i class="bi bi-collection-play-fill text-info me-2"></i>
                                            {{ $formation->courses->count() }} cours
                                        </small>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </fieldset>

                    <!-- Container pour les cours et chapitres -->
                    <div class="container-fluid mt-5">
                        <div class="row g-4">
                            <!-- Colonne des cours -->
                            <div class="col-md-8">
                                <fieldset class="border p-3 shadow-sm">
                                    <legend class="fw-bold text-muted" style="margin-top:-40px;">

                                        <span class="badge bg-success"><i class="bi bi-book text-white me-2"></i> Les
                                            derniers cours</span>
                                    </legend>
                                    <ul class="list-group list-group-flush">
                                        @foreach ($courses as $course)
                                            <li class="list-group-item  d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="fw-bold mb-0">
                                                        <i class="bi bi-journal-code text-warning me-2"></i>
                                                        {{ $course->title }}
                                                    </h6>
                                                    <small class="text-muted">
                                                        <i class="bi bi-clock-fill text-info me-2"></i>
                                                        Durée : {{ $course->duration }} min
                                                    </small>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </fieldset>
                            </div>

                            <!-- Colonne des chapitres -->
                            <div class="col-md-4">
                                <fieldset class="border p-3 shadow-sm">
                                    <legend class="fw-bold text-muted" style="margin-top:-40px;">

                                        <span class="badge bg-danger"><i class="bi bi-book-half text-white me-2"></i> Les
                                            derniers chapitres</span>
                                    </legend>
                                    <ul class="list-group list-group-flush">
                                        @foreach ($chapters as $chapitre)
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="fw-bold mb-0">
                                                        <i class="bi bi-layers-half text-danger me-2"></i>
                                                        {{ $chapitre->title }}
                                                    </h6>
                                                    <small class="text-muted">
                                                        <i class="bi bi-calendar-event-fill text-secondary me-2"></i>
                                                        Ajouté le {{ $chapitre->created_at->format('d M Y') }}
                                                    </small>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>




        <div class="d-md-flex flex-md-equal w-100 my-md-3 ps-md-3">
            <div class="bg-body-tertiary me-md-3 pt-3 px-3 pt-md-5 px-md-5 text-center overflow-hidden">
                <div class="my-3 p-3">
                    <h2 class="display-5">Another headline</h2>
                    <p class="lead">And an even wittier subheading.</p>
                </div>
                <div class="bg-dark shadow-sm mx-auto" style="width: 80%; height: 300px; border-radius: 21px 21px 0 0;">
                </div>
            </div>
            <div class="text-bg-primary me-md-3 pt-3 px-3 pt-md-5 px-md-5 text-center overflow-hidden">
                <div class="my-3 py-3">
                    <h2 class="display-5">Another headline</h2>
                    <p class="lead">And an even wittier subheading.</p>
                </div>
                <div class="bg-body-tertiary shadow-sm mx-auto"
                    style="width: 80%; height: 300px; border-radius: 21px 21px 0 0;"></div>
            </div>
        </div>

        <div class="d-md-flex flex-md-equal w-100 my-md-3 ps-md-3">
            <div class="bg-body-tertiary me-md-3 pt-3 px-3 pt-md-5 px-md-5 text-center overflow-hidden">
                <div class="my-3 p-3">
                    <h2 class="display-5">Another headline</h2>
                    <p class="lead">And an even wittier subheading.</p>
                </div>
                <div class="bg-body shadow-sm mx-auto" style="width: 80%; height: 300px; border-radius: 21px 21px 0 0;">
                </div>
            </div>
            <div class="bg-body-tertiary me-md-3 pt-3 px-3 pt-md-5 px-md-5 text-center overflow-hidden">
                <div class="my-3 py-3">
                    <h2 class="display-5">Another headline</h2>
                    <p class="lead">And an even wittier subheading.</p>
                </div>
                <div class="bg-body shadow-sm mx-auto" style="width: 80%; height: 300px; border-radius: 21px 21px 0 0;">
                </div>
            </div>
        </div>

        <div class="d-md-flex flex-md-equal w-100 my-md-3 ps-md-3">
            <div class="bg-body-tertiary me-md-3 pt-3 px-3 pt-md-5 px-md-5 text-center overflow-hidden">
                <div class="my-3 p-3">
                    <h2 class="display-5">Another headline</h2>
                    <p class="lead">And an even wittier subheading.</p>
                </div>
                <div class="bg-body shadow-sm mx-auto" style="width: 80%; height: 300px; border-radius: 21px 21px 0 0;">
                </div>
            </div>
            <div class="bg-body-tertiary me-md-3 pt-3 px-3 pt-md-5 px-md-5 text-center overflow-hidden">
                <div class="my-3 py-3">
                    <h2 class="display-5">Another headline</h2>
                    <p class="lead">And an even wittier subheading.</p>
                </div>
                <div class="bg-body shadow-sm mx-auto" style="width: 80%; height: 300px; border-radius: 21px 21px 0 0;">
                </div>
            </div>
        </div>
    </main>
@endsection
