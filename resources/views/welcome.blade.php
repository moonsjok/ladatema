@extends('layouts.guest.index')
@section('page-title', 'Bienvenue')
@section('content')




    <div id="carouselExampleDark" class="carousel carousel-dark slide" data-bs-ride="carousel">
        <div class="carousel-indicators ">
            <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="0" class="active" aria-current="true"
                aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner p-5">

            <!-- Premier slide -->
            <div class="carousel-item active" data-bs-interval="5000">
                <div class="container h-100 p-4">
                    <div class="row align-items-center h-100">
                        <div class="col-md-6 text-center text-md-start d-none d-md-block">
                            <img src="{{ asset('images/ELI_LAWSON.png') }}" class="img-fluid" alt="Image entreprise">
                        </div>
                        <div class="col-md-6 text-center text-md-start">
                            <h1 class="fw-bold display-6">Votre cabinet d'experts comptables</h1>
                            <h3 class="subtitle">
                                <strong>Créateur</strong>, <strong>repreneur</strong> ou <strong>chef d’entreprise</strong>,
                                nos experts comptables vous accompagnent pour <strong>gérer et développer votre
                                    entreprise.</strong>
                            </h3>
                            <p class="mt-4">
                                <a href="#" class="btn btn-primary btn-lg  mt-3">
                                    <i class="bi bi-briefcase"></i> Nos prestations
                                </a>
                                <a href="#" class="btn btn-secondary btn-lg  mt-3">
                                    <i class="bi bi-chat-dots"></i> Nous contacter
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Deuxième slide - Formation BRVM -->
            <div class="carousel-item" data-bs-interval="5000">
                <div class="container h-100 p-4">
                    <div class="row align-items-center h-100">
                        <div class="col-md-6 text-center text-md-start">
                            <h1 class="fw-bold display-6">Formations en Bourse - BRVM</h1>
                            <h3 class="subtitle">
                                Apprenez à investir en toute confiance sur la BRVM et optimisez vos placements.
                            </h3>
                            <p class="mt-4">
                                <a class="btn btn-warning btn-lg mt-3" href="{{ route('register') }}">
                                    <i class="bi bi-person-plus"></i> Créer un compte
                                </a>
                                <a class="btn btn-dark btn-lg text-white  mt-3" href="{{ route('guest.formationsList') }}">
                                    <i class="bi bi-book"></i> Parcourir les formations
                                </a>
                            </p>
                        </div>
                        <div class="col-md-6 text-center d-none d-md-block">
                            <img src="{{ asset('images/brvm.png') }}" class="img-fluid " alt="Formation BRVM">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Troisième slide - Formalisation d'entreprise -->
            <div class="carousel-item" data-bs-interval="5000">
                <div class="container-fluid h-100 p-4"
                    style="background: url('{{ asset('images/bureau.jpg') }}') center/cover; border-radius: 15px;">
                    <div class="row align-items-center h-100 ">
                        <div class="col-md-6 text-center text-md-start p-2" style="background-color:rgba(255,255,255,0.7)">
                            <h1 class="fw-bold display-6">Lancez votre entreprise dès maintenant !</h1>
                            <h3 class="subtitle">
                                Formalité de création d'entreprise en 48h
                                <br />
                                En toute simplicité, sans vous déplacer !
                            </h3>
                            <p class="mt-4">
                                <a class="btn btn-primary btn-lg mt-3" href="{{ route('contact.form') }}">
                                    <i class="bi bi-envelope"></i> Contactez-nous
                                </a>
                            </p>
                        </div>
                        <div class="col-md-6 text-center d-none d-md-block">
                            {{-- <img src="{{ asset('images/bureau.jpg') }}" class="img-fluid rounded-5"
                                alt="Création d'entreprise"> --}}
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Contrôles -->
        {{-- <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleDark" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Précédent</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleDark" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Suivant</span>
    </button> --}}
    </div>




    <main>

        <div class="container-fluid p-5  bg-white">
            {{-- <h2 class="text-center mb-4"><i class="bi bi-briefcase"></i> Nos Services</h2> --}}

            <div class="row row-cols-1 row-cols-md-2 g-4 ">
                <!-- Assistance Comptable et Fiscale -->
                <div class="col">
                    <a href="#" class="text-decoration-none text-reset service-link">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="bi bi-calculator display-2 mb-3 text-primary"></i>
                                <h2 class="fw-bold card-title">ASSISTANCE COMPTABLE ET FISCALE</h2>
                                <p class="card-text">Nous offrons des solutions adaptées pour garantir la conformité de vos
                                    déclarations, optimiser votre fiscalité et vous fournir un suivi rigoureux de votre
                                    comptabilité. Avec notre expertise, vous pouvez vous concentrer sur le développement de
                                    votre activité en toute sérénité.</p>
                                {{-- <button class="btn btn-primary">En savoir plus</button> --}}
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Représentation et Assistance Commerciale -->
                <div class="col">
                    <a href="#" class="text-decoration-none text-reset service-link">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="bi bi-people-fill display-2 mb-3 text-primary"></i>
                                <h2 class="fw-bold card-title">REPRESENTATION ET ASSISTANCE COMMERCIALE</h2>
                                <p class="card-text">Notre équipe se charge de promouvoir vos intérêts, de gérer vos
                                    relations commerciales et de vous assister dans vos démarches stratégiques, afin de
                                    maximiser vos opportunités et renforcer votre position sur le marché.</p>
                                {{-- <button class="btn btn-primary">En savoir plus</button> --}}
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>


        <div class="container-fluid formations">

            {{-- <br /> <br /> --}}

            <div class="row">
                <div class="col-md-8 mx-auto text-center">
                    <h2 class="fw-bold display-5 ">
                        <i class="bi bi-mortarboard-fill text-primary me-2"></i>
                        Formations
                    </h2>
                    <p class="text-muted display-6  ">
                        Développez les compétences de votre équipe avec nos formations.
                    </p>
                    <!-- Ajoutez ici le reste du texte si nécessaire -->
                </div>
            </div>
        </div>

        @push('styles')
            <style>
                .formations {
                    color: #000;
                    position: relative;
                }

                .formations .row {

                    height: 100vh;
                    /* background-image: url('{{ asset('images/POST_FORMATION-EN-LIGNE.jpg') }}');*/
                    background-image: url('{{ asset('images/formation.jpg') }}');
                    background-size: cover;
                    background-position: top center;
                    background-repeat: no-repeat;
                }

                .formations .col-md-8 {
                    position: absolute;
                    bottom: 0;
                    left: 50%;
                    transform: translateX(-50%);
                    background-color: rgba(255, 255, 255, 0.9);
                    padding: 5rem;
                }
            </style>
        @endpush

        <div class="container-fluid .my-5 p-5">
            {{-- <div class="row  g-4 p-5 mb-5  bg-white ">
                <div class="col-12">
                    <h2 class="fw-bold display-5 ">
                        <i class="bi bi-mortarboard-fill text-primary me-2"></i>
                        Formations
                    </h2>
                    <p class="text-muted display-6  ">
                        Développez les compétences de votre équipe avec nos formations.
                    </p>
                </div>
            </div> --}}

            <div class="row g-4">
                <!-- Première colonne : Description -->
                <div class="col-md-4">
                    <div class="p-3 border rounded shadow-sm  bg-white ">
                        <h2 class="fw-bold display-5">
                            Votre argent mérite mieux que 3,5%*
                        </h2>
                        <p class="text-muted display-6  ">
                            <strong class="text-primary">Apprenez</strong> comment investir
                            et <strong class="text-primary">gagnez davantage</strong> grâce à la BRVM
                            <br>
                            <small>*Taux d’intérêt créditeur pour les comptes d’épargne simples, dans l’UEMOA</small>
                        </p>

                        <div class="d-grid gap-2">
                            <a class="btn btn-primary btn-lg text-white mt-3 fw-bold"
                                href="{{ route('guest.formationsList', ['selectedCategory' => 3]) }}">
                                <i class="bi bi-search"></i> Parcourir les formations <span
                                    class="text-dark">Ladabourse</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Deuxième colonne : Formations avec Cours et Chapitres -->
                <div class="col-md-8">
                    <!-- Bloc des formations -->
                    <fieldset class="border p-3 shadow-sm mb-4  bg-white">
                        <legend class="fw-bold text-muted" style="margin-top:-40px;">
                            <a href="{{ route('guest.formationsList') }}"
                                class="text-decoration-none  badge bg-primary rounded">
                                <i class="bi bi-mortarboard-fill text-white me-2"></i>
                                Les dernières formations</a>
                        </legend>
                        <ul class="list-group list-group-flush">
                            @foreach ($formations as $formation)
                                <a href="{{ route('guest.formations.show', $formation) }}"
                                    class="list-group-item  list-group-item-action d-flex justify-content-between align-items-center  bg-white">
                                    <div>
                                        <h6 class="fw-bold mb-0">
                                            <i class="bi bi-bookmark-star text-success me-2"></i> {{ $formation->title }}
                                        </h6>
                                        <small class="text-muted">
                                            <i class="bi bi-collection-play-fill text-info me-2"></i>
                                            {{ $formation->courses->count() }} cours
                                        </small>
                                    </div>
                                </a>
                            @endforeach
                        </ul>
                    </fieldset>

                    <!-- Container pour les cours et chapitres -->
                    <div class="container-fluid mt-5 p-0">
                        <div class="row g-4 ">
                            <!-- Colonne des cours -->
                            <div class="col-md-8 ">
                                <fieldset class="border p-3 shadow-sm  bg-white">
                                    <legend class="fw-bold text-muted" style="margin-top:-40px;">

                                        <span class="badge bg-success"><i class="bi bi-book text-white me-2"></i> Les
                                            Derniers cours</span>
                                    </legend>
                                    <ul class="list-group list-group-flush">
                                        @foreach ($courses as $course)
                                            <a href="{{ route('course-viewer', [
                                                'course' => $course->id,
                                                'chapterId' => '',
                                                'type' => 'formation',
                                                'id' => $course->formation->id,
                                            ]) }}"
                                                class="list-group-item list-group-item-action  d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="fw-bold mb-0">
                                                        <i class="bi bi-journal-code text-warning me-2"></i>
                                                        {{ $course->title }}
                                                    </h6>
                                                    <small class="text-muted">
                                                        <i class="bi bi-clock-fill text-info me-2"></i>
                                                        Durée : {{ $course->duration ?? 'N/A' }}
                                                    </small>
                                                </div>
                                            </a>
                                        @endforeach
                                    </ul>
                                </fieldset>
                            </div>

                            <!-- Colonne des chapitres -->
                            <div class="col-md-4">
                                <fieldset class="border p-3 shadow-sm  bg-white">
                                    <legend class="fw-bold text-muted" style="margin-top:-40px;">

                                        <span class="badge bg-danger"><i class="bi bi-book-half text-white me-2"></i> Les
                                            Derniers chapitres</span>
                                    </legend>
                                    <ul class="list-group list-group-flush">
                                        @foreach ($chapters as $chapitre)
                                            <a href="{{ route('course-viewer', [
                                                'course' => $chapitre->course->id,
                                                'chapterId' => $chapitre->id,
                                                'type' => 'formation',
                                                'id' => $chapitre->course->formation->id,
                                            ]) }}"
                                                class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="fw-bold mb-0">
                                                        <i class="bi bi-layers-half text-danger me-2"></i>
                                                        {{ $chapitre->title }}
                                                    </h6>
                                                    <small class="text-muted">
                                                        <i class="bi bi-calendar-fill text-secondary me-2"></i>
                                                        Ajouté le {{ $chapitre->created_at->format('d M Y') }}
                                                    </small>
                                                </div>
                                            </a>
                                        @endforeach
                                    </ul>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid text-center bg-white ">
            <div class="row p-5">
                <div class="col-12">
                    <h2 class="fw-bold display-5 ">Recrutement</h2>
                    <h3 class="text-muted display-6  ">Trouvez les talents, bâtissez l'avenir.</h3>
                    <p>Rejoignez-nous pour une croissance durable !</p>
                </div>
            </div>
            <div class="row">
                <div class="col-12 talents ">

                </div>
            </div>

            <div class="row bg-primary text-center text-white">
                <div class="col-12 p-5 ">
                    <h2 class="display-6">
                        Renforcer les entreprises grâce à des solutions RH sur mesure.
                    </h2>
                    <p class="">Découvrez les bons talents dès aujourd'hui !</p>
                    <a href="{{ route('contact.form') }}" class="btn btn-secondary btn-lg">Nous contacter</a>
                </div>
            </div>


        </div>
        @push('styles')
            <style>
                .talents {
                    text-align: center !important;
                    height: 600px !important;
                    background-image: url('{{ asset('images/talents.png') }}');
                    background-size: cover;
                    background-position: center top;
                    background-repeat: no-repeat;
                }
            </style>
        @endpush




        <div class="containerfluid  bg-light p-5 mt-5 text-center">
            <h2 class="mb-4"><i class="bi bi-check-circle-fill text-primary"></i> Pourquoi nous choisir ?</h2>

            <div class="row row-cols-1 row-cols-md-3 g-4">
                <!-- Analyse rigoureuse -->
                <div class="col">
                    <div class="card h-100">
                        <div class="card-body">
                            <i class="bi bi-bar-chart-fill display-4 mb-3 text-primary"></i>
                            <h5 class="card-title">Analyse Rigoureuse</h5>
                            <p class="card-text">Un examen approfondi des éléments clés qui influencent la santé financière
                                de votre entreprise.</p>
                        </div>
                    </div>
                </div>

                <!-- Idées Innovantes -->
                <div class="col">
                    <div class="card h-100">
                        <div class="card-body">
                            <i class="bi bi-lightbulb-fill display-4 mb-3 text-primary"></i>
                            <h5 class="card-title">Idées Innovantes</h5>
                            <p class="card-text">Notre créativité est au cœur de tout ce que nous faisons. Solutions
                                innovantes pour votre projet.</p>
                        </div>
                    </div>
                </div>

                <!-- Engagement et Personnalisation -->
                <div class="col">
                    <div class="card h-100">
                        <div class="card-body">
                            <i class="bi bi-heart-fill display-4 mb-3 text-primary"></i>
                            <h5 class="card-title">Engagement & Personnalisation</h5>
                            <p class="card-text">Nous plaçons vos objectifs au centre, aidant à l'entrée en bourse et
                                optimisant votre gestion financière.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mt-5">
            <h2 class="text-center mb-4">Foire aux questions</h2>
            <p class="text-center mb-5">Dans cette section, nous répondons aux questions les plus fréquentes sur nos
                services comptables.</p>

            <div class="accordion" id="faqAccordion">
                <!-- Question 1 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            Quels sont les avantages de travailler avec votre cabinet ?
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                        data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Notre cabinet d'expertise comptable se spécialise dans la gestion financière, la fiscalité et
                            l'audit. Nous offrons des solutions personnalisées pour accompagner les entreprises dans leur
                            croissance et leur conformité.
                        </div>
                    </div>
                </div>

                <!-- Question 2 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            Comment puis-je contacter votre cabinet ?
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                        data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Contactez notre équipe d'experts-comptables par e-mail à <a
                                href="mailto:Ladatema@gmail.com">Ladatema@gmail.com</a>, ou utilisez notre formulaire de
                            contact sur notre site. Nous sommes disponibles pour vous aider du lundi au vendredi.
                        </div>
                    </div>
                </div>

                <!-- Question 3 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            Quels services comptables offrez-vous ?
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                        data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Nous offrons une consultation initiale pour tous nos nouveaux clients. Les services sont
                            personnalisés selon vos besoins spécifiques et nous garantissons une réponse rapide à toutes vos
                            demandes.
                        </div>
                    </div>
                </div>

                <!-- Question 4 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingFour">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                            Nous nous engageons à fournir des solutions comptables précises et efficaces pour garantir votre
                            tranquillité d'esprit.
                        </button>
                    </h2>
                    <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour"
                        data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <!-- N/A - This was more of a statement than a question, so no content is added -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.partials.logo-slider')
    </main>
    @push('styles')
        <style>
            .carousel-item {
                min-height: 500px;
                height: 500px;
            }

            .carousel-item .container {
                height: 100%;
            }

            /* Centrage sur mobile */
            @media (max-width: 768px) {
                .carousel-item .row {
                    text-align: center;
                }

                .carousel-item img {
                    max-width: 80%;
                    margin: auto;
                }
            }
        </style>
    @endpush
    @push('styles')
        <style>
            .service-link .card {
                transition: all 0.3s ease;
            }


            .service-link:hover .card,
            .service-link:focus .card {
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
                transform: scale(1.02);
            }

            .service-link:hover .bi,
            .service-link:focus .bi {
                color: 0 0 10px rgba(0, 0, 0, 0.2);

            }

            .service-link:hover .card-title,
            .service-link:focus .card-title {
                color: #0d6efd;
                /* Couleur du texte au survol ou focus */
            }

            .service-link:hover .card-text,
            .service-link:focus .card-text {
                color: #0d6efd;
                /* Assurez-vous que le texte reste lisible */
            }
        </style>
    @endpush
@endsection
