@extends('layouts.authenticated.owners.index')
@section('page-title', "Détails de l'évaluation")

@section('dashboard-content')
    <div class="container my-4">
        <div class="card">
            <div class="card-body">
                <h1 class="card-title">
                    <i class="bi bi-clipboard"></i> {{ $evaluation->title }}
                    <a href="{{ route('evaluations.edit.step1', $evaluation->id) }}" class="btn btn-warning btn-sm float-end">
                        <i class="bi bi-pencil"></i> Modifier l'évaluation
                    </a>
                </h1>
                <p class="card-text">
                    <i class="bi bi-info-circle"></i> {!! $evaluation->description !!}
                </p>
                <p class="card-text">
                    <strong><i class="bi bi-tags"></i> Évaluation de fin de :</strong>
                    @if ($evaluation->evaluatable_type === 'App\\Models\\Formation')
                        Formation
                    @elseif($evaluation->evaluatable_type === 'App\\Models\\Course')
                        Cours
                    @elseif($evaluation->evaluatable_type === 'App\\Models\\Chapter')
                        Chapitre
                    @endif
                </p>

                @if ($evaluation->evaluatable)
                    <p class="card-text">
                        <strong><i class="bi bi-file-earmark-text"></i>
                            @if ($evaluation->evaluatable_type === 'App\\Models\\Formation')
                                Formation
                            @elseif($evaluation->evaluatable_type === 'App\\Models\\Course')
                                Cours
                            @elseif($evaluation->evaluatable_type === 'App\\Models\\Chapter')
                                Chapitre
                            @endif Intitulé :
                        </strong> {{ $evaluation->evaluatable->title }}
                        <a href="#detailsModal" data-bs-toggle="modal" class="btn btn-link">
                            <i class="bi bi-eye"></i> Voir les détails
                        </a>
                    <div class="container">
                        <!-- Informations détaillées -->
                        <div class="row">
                            <div class="col-md-3">
                                <div class="text-center p-3 bg-light rounded">
                                    <small class="text-muted">Questions</small>
                                    <div class="h5 mb-1">{{ $evaluation->questions->count() }}</div>

                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center p-3 bg-light rounded">
                                    <small class="text-muted">Durée (min)</small>
                                    <div class="h5 mb-1">{{ $evaluation->duration ?? 'Illimité' }}</div>

                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center p-3 bg-light rounded">
                                    <small class="text-muted">Tentatives max</small>
                                    <div class="h5 mb-1">{{ $evaluation->max_attempts ?? 'Illimité' }}</div>

                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center p-3 bg-light rounded">
                                    <small class="text-muted">Score requis</small>
                                    <div class="h5 mb-1">
                                        {{ $evaluation->passing_score ?? 60 }}
                                        {{ $evaluation->scoring_mode == 'points' ? ' Points' : '  %' }}


                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                    </p>
                @endif
            </div>
        </div>

        <!-- Ajouter une nouvelle question et ses reponses -->
        <div class="mt-4">
            @livewire('question-form', ['evaluationId' => $evaluation->id])
        </div>
        @if (sizeOf($evaluation->questions) === 0)
            <div class=" card text-center p-3 ">

                <div class="text-center text-muted my-3">
                    <i class="bi bi-info-circle-fill" style="font-size: 1.5rem;"></i>
                    <strong class="d-block mt-2"><i>Il n'y a encore aucune question.</i></strong>
                </div>


            </div>
        @else
            <!-- Accordéon pour les questions et réponses -->
            <div class="accordion mt-4" id="questionsAccordion">
                @foreach ($evaluation->questions as $index => $question)
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading{{ $index }}">
                            <button class="accordion-button @if ($index !== 0) collapsed @endif"
                                type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}"
                                aria-expanded="@if ($index === 0) true @else false @endif"
                                aria-controls="collapse{{ $index }}">
                                <i class="bi bi-question-circle"></i>&nbsp;&nbsp; Question {{ $index + 1 }} :
                                {{ $question->question_text }}
                                <span class="badge bg-secondary ms-2">{{ $question->getTypeLabel() }}</span>
                                @if ($evaluation->scoring_mode === 'points')
                                    <span class="badge bg-primary ms-2">{{ $question->points }} points</span>
                                @endif
                                <a href="{{ route('evaluation.question.edit', $question->id) }}"
                                    class="btn btn-sm btn-outline-primary ms-2" data-bs-toggle="tooltip"
                                    data-bs-placement="top" title="Modifier">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="#" class="btn btn-sm btn-outline-danger ms-2" data-bs-toggle="modal"
                                    data-bs-target="#deleteQuestionModal{{ $question->id }}" data-bs-toggle="tooltip"
                                    data-bs-placement="top" title="Supprimer">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </button>
                        </h2>
                        <div id="collapse{{ $index }}"
                            class="accordion-collapse collapse @if ($index === 0) show @endif"
                            aria-labelledby="heading{{ $index }}" data-bs-parent="#questionsAccordion">
                            <div class="accordion-body">
                                @if ($question->type === 'text')
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle"></i> Question de type texte - réponse libre de
                                        l'étudiant
                                    </div>
                                @elseif($question->answers->count() > 0)
                                    <ul class="list-group">
                                        @foreach ($question->answers as $answer)
                                            <li class="list-group-item">
                                                <!-- Ligne principale : numérotation, texte, badges, boutons -->
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <div class="d-flex align-items-center">
                                                        <span class="me-2 fw-bold">{{ chr(65 + $loop->index) }})</span>
                                                        {{ $answer->answer_text }}
                                                    </div>
                                                    <span>
                                                        @if ($question->type === 'find_intruder')
                                                            @if (!$answer->is_correct)
                                                                <span class="badge bg-success"><i
                                                                        class="bi bi-check-circle"></i> Intrus</span>
                                                            @endif
                                                        @else
                                                            @if ($answer->is_correct)
                                                                <span class="badge bg-success"><i
                                                                        class="bi bi-check-circle"></i> Correct</span>
                                                            @endif
                                                        @endif
                                                    </span>
                                                </div>

                                                <!-- Ligne d'explication séparée en dessous -->
                                                @if ($answer->explanation)
                                                    <div class="ms-4">
                                                        <div class="alert alert-info py-2 px-3 mb-0">
                                                            <small class="text-muted">
                                                                <i class="bi bi-info-circle me-1"></i>
                                                                <strong>Explication :</strong> {{ $answer->explanation }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="bi bi-exclamation-triangle"></i> Aucune réponse définie
                                    </div>
                                @endif
                            </div>
                        </div>


                        <!-- Modal de confirmation de suppression pour la question -->
                        <div class="modal fade" id="deleteQuestionModal{{ $question->id }}" tabindex="-1"
                            aria-labelledby="deleteQuestionModalLabel{{ $question->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteQuestionModalLabel{{ $question->id }}">
                                            <i class="bi bi-exclamation-triangle"></i> Confirmer la suppression
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Êtes-vous sûr de vouloir supprimer cette question ?</p>
                                        <div class="alert alert-warning">
                                            <strong>Question :</strong> {{ $question->question_text }}
                                        </div>
                                        <p class="text-muted">Cette action est irréversible.</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Annuler</button>
                                        <form action="{{ route('evaluation.question.delete', $question->id) }}"
                                            method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">
                                                <i class="bi bi-trash"></i> Supprimer
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif



    </div>

    <!-- Modal pour les détails de la formation/cours/chapitre -->
    <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailsModalLabel">
                        <i class="bi bi-info-circle"></i> Détails de {{ $evaluation->evaluatable->title }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong><i class="bi bi-info-square"></i> Description :</strong>
                        {!! $evaluation->evaluatable->description !!}</p>

                    @if ($evaluation->evaluatable_type === 'App\\Models\\Formation')
                        <p><strong><i class="bi bi-clock"></i> Durée :</strong>
                            {{ $evaluation->evaluatable->duration ?? 'N/A' }}
                        </p>
                    @elseif($evaluation->evaluatable_type === 'App\\Models\\Course')
                        <p><strong><i class="bi bi-journal"></i> Nombre de chapitres :</strong>
                            {{ $evaluation->evaluatable->chapters_count }}</p>
                    @elseif($evaluation->evaluatable_type === 'App\\Models\\Chapter')
                        <p><strong><i class="bi bi-list-ul"></i> Contenu :</strong>
                            {!! $evaluation->evaluatable->content !!}</p>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>



@endsection
