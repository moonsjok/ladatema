@extends('layouts.authenticated.owners.index')
@section('page-title', "Détails de l'évaluation")

@section('dashboard-content')
    <div class="container my-4">
        <div class="card">
            <div class="card-body">
                <h1 class="card-title">
                    <i class="bi bi-clipboard"></i> {{ $evaluation->title }}
                    <a href="{{ route('evaluations.edit', $evaluation->id) }}" class="btn btn-warning btn-sm float-end">
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
                    </p>
                @endif
            </div>
        </div>

        <!-- Ajouter une nouvelle question et ses reponse -->
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
                                <a href="#" class="btn btn-sm btn-outline-primary ms-2" data-bs-toggle="modal"
                                    data-bs-target="#editQuestionModal{{ $question->id }}">
                                    <i class="bi bi-pencil"></i> Modifier
                                </a>
                            </button>
                        </h2>
                        <div id="collapse{{ $index }}"
                            class="accordion-collapse collapse @if ($index === 0) show @endif"
                            aria-labelledby="heading{{ $index }}" data-bs-parent="#questionsAccordion">
                            <div class="accordion-body">
                                <ul class="list-group">
                                    @foreach ($question->answers as $answer)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>
                                                <i class="bi bi-check2-circle"></i> {{ $answer->answer_text }}
                                            </span>
                                            <span>
                                                @if ($answer->is_correct)
                                                    <span class="badge bg-success"><i class="bi bi-check-circle"></i>
                                                        Correct</span>
                                                @endif
                                                <a href="#" class="btn btn-sm btn-outline-secondary ms-2"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editAnswerModal{{ $answer->id }}">
                                                    <i class="bi bi-pencil"></i> Modifier
                                                </a>
                                            </span>
                                        </li>

                                        <!-- Modal pour modifier une réponse -->
                                        <div class="modal fade" id="editAnswerModal{{ $answer->id }}" tabindex="-1"
                                            aria-labelledby="editAnswerModalLabel{{ $answer->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title"
                                                            id="editAnswerModalLabel{{ $answer->id }}">Modifier la
                                                            réponse</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="{{ route('evaluation.answer.update', $answer->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="mb-3">
                                                                <label for="answerText{{ $answer->id }}"
                                                                    class="form-label">Texte de la réponse</label>
                                                                <input type="text" class="form-control"
                                                                    id="answerText{{ $answer->id }}" name="answer_text"
                                                                    value="{{ $answer->answer_text }}">
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="hidden"
                                                                    name="is_correct" value="0">
                                                                <input class="form-check-input" type="checkbox"
                                                                    id="isCorrect{{ $answer->id }}" name="is_correct"
                                                                    value="1"
                                                                    @if ($answer->is_correct) checked @endif>
                                                                <label class="form-check-label"
                                                                    for="isCorrect{{ $answer->id }}">
                                                                    Correcte
                                                                </label>
                                                            </div>
                                                            <button type="submit"
                                                                class="btn btn-primary mt-3">Enregistrer</button>
                                                        </form>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Fermer</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <!-- Modal pour modifier une question -->
                        <div class="modal fade" id="editQuestionModal{{ $question->id }}" tabindex="-1"
                            aria-labelledby="editQuestionModalLabel{{ $question->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editQuestionModalLabel{{ $question->id }}">Modifier
                                            la question</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('evaluation.question.update', $question->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="mb-3">
                                                <label for="questionText{{ $question->id }}" class="form-label">Texte de
                                                    la question</label>
                                                <input type="text" class="form-control"
                                                    id="questionText{{ $question->id }}" name="question_text"
                                                    value="{{ $question->question_text }}">
                                            </div>
                                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Fermer</button>
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
