@extends('layouts.authenticated.students.index')
@section('page-title', 'Tableau de bord')
@section('dashboard-content')

    <div class="container-fluid mt-0">
        <div class="row">
            <div class="col-md-8 p-3">
                <h1><i class="bi bi-mortarboard-fill"></i> Mes Formations</h1>

                @if ($formations->isEmpty())
                    <p class="text-justify mt-3">
                    <p class="fw-bold">Explorez nos parcours!</p>
                    Découvrez une nouvelle façon d'apprendre avec <strong>{{ env('APP_NAME') }}</strong> !
                    Accédez à des formations interactives et adaptées à votre rythme, conçues pour vous aider à développer
                    vos compétences et booster votre carrière.
                    Explorez nos parcours personnalisés et commencez dès aujourd'hui votre apprentissage !
                    </p>
                    <a href="{{ route('guest.formationsList') }}" class="btn btn-primary">
                        <i class="bi bi-arrow-right-circle"></i> Parcourir le catalogue
                    </a>
                @else
                    <div class="mt-4">
                        {{-- <h4><i class="bi bi-journal-bookmark-fill"></i> Vos formations</h4> --}}
                        <div class="accordion" id="formationsAccordion">
                            @foreach ($formations as $formation)
                                @if ($formation)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading{{ $formation->id }}">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapse{{ $formation->id }}"
                                                aria-expanded="false" aria-controls="collapse{{ $formation->id }}">
                                                <i class="bi bi-book-fill me-2"></i> {{ $formation->title }}
                                            </button>
                                        </h2>
                                        <div id="collapse{{ $formation->id }}" class="accordion-collapse collapse"
                                            aria-labelledby="heading{{ $formation->id }}"
                                            data-bs-parent="#formationsAccordion">
                                            <div class="accordion-body">
                                                <p class="text-justify">{!! $formation->description !!}</p>


                                                <h6><i class="bi bi-journal-text"></i> Liste des cours :</h6>
                                                <div class="list-group">
                                                    @foreach ($formation->courses as $course)
                                                        <div
                                                            class="list-group-item .d-flex .justify-content-between .align-items-center ">
                                                            <a href="{{ route('course-viewer', [$course]) }}"
                                                                class="d-flex align-items-center text-decoration-none">
                                                                {{-- <i class="bi bi-play-circle"></i> --}}
                                                                {{ $course->title }}
                                                            </a>
                                                            <hr />
                                                            <button
                                                                class="btn btn-sm btn-outline-primary show-course-details "
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#course{{ $course->id }}">
                                                                <i class="bi bi-chevron-down"></i>
                                                                {{ $course->chapters->count() }} chapitre(s)
                                                            </button>
                                                        </div>
                                                        <div id="course{{ $course->id }}" class="collapse mt-2">
                                                            <div class="list-group list-group-flush">
                                                                @foreach ($course->chapters as $chapter)
                                                                    <div
                                                                        class="list-group-item d-flex justify-content-between">
                                                                        <a href="{{ route('course-viewer', [
                                                                            'course' => $course->id,
                                                                            'chapterId' => '',
                                                                            'type' => 'formation',
                                                                            'id' => $course->formation->id,
                                                                        ]) }}"
                                                                            class="d-flex align-items-center text-decoration-none">
                                                                            <i class="bi bi-book"></i>
                                                                            {{ $chapter->title }}
                                                                        </a>
                                                                        <a href="{{ route('course-viewer', [
                                                                            'course' => $course->id,
                                                                            'chapterId' => '',
                                                                            'type' => 'formation',
                                                                            'id' => $course->formation->id,
                                                                        ]) }}"
                                                                            class="btn btn-sm btn-outline-secondary text-decoration-none">
                                                                            <i class="bi bi-eye"></i> Accéder
                                                                        </a>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>


                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-md-4 bg-light min-vh-100 d-flex flex-column p-3">
                <p class="w-100 d-flex justify-content-between align-items-center">
                    <strong>Dernières réalisations</strong>
                    <a href="#" class="text-end">Afficher tout</a>
                </p>
            </div>
        </div>
    </div>

@endsection
