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
                            {{$evaluation->scoring_mode == "points" ? " Points" : "  %"}}
                          
                            
                            </div>
                           
                        </div>
                    </div>
                </div>
                        
                        </div>
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
                                <span class="badge bg-secondary ms-2">{{ $question->getTypeLabel() }}</span>
                                @if($evaluation->scoring_mode === 'points')
                                    <span class="badge bg-primary ms-2">{{ $question->points }} points</span>
                                @endif
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
                                @if($question->type === 'text')
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle"></i> Question de type texte - réponse libre de l'étudiant
                                    </div>
                                @elseif($question->answers->count() > 0)
                                    <ul class="list-group">
                                        @foreach ($question->answers as $answer)
                                            <li class="list-group-item">
                                                <!-- Ligne principale : numérotation, texte, badges, boutons -->
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <div class="d-flex align-items-center">
                                                        <span class="me-2 fw-bold">{{ chr(65 + $loop->index) }})</span>
                                                        <i class="bi bi-check2-circle"></i> {{ $answer->answer_text }}
                                                    </div>
                                                    <span>
                                                        @if($question->type === 'find_intruder')

                                                            @if(!$answer->is_correct)
                                                                 <span class="badge bg-success"><i class="bi bi-check-circle"></i>  Intrus</span>
                                                            @endif

                                                        @else
                                                            @if ($answer->is_correct)
                                                                <span class="badge bg-success"><i class="bi bi-check-circle"></i> Correct</span>
                                                            @endif
                                                        @endif
                                                        <a href="#" class="btn btn-sm btn-outline-secondary ms-2"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editAnswerModal{{ $answer->id }}">
                                                            <i class="bi bi-pencil"></i> Modifier
                                                        </a>
                                                    </span>
                                                </div>
                                                
                                                <!-- Ligne d'explication séparée en dessous -->
                                                @if($answer->explanation)
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
                                                                <div class="mb-3">
                                                                    <label for="answerExplanation{{ $answer->id }}"
                                                                        class="form-label">Explication (optionnelle)</label>
                                                                    <textarea class="form-control"
                                                                        id="answerExplanation{{ $answer->id }}" name="explanation"
                                                                        rows="3">{{ $answer->explanation ?? '' }}</textarea>
                                                                </div>
                                                                @if($question->type === 'find_intruder')
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            name="is_correct" value="0"
                                                                            id="isIntruder{{ $answer->id }}"
                                                                            @if (!$answer->is_correct) checked @endif>
                                                                        <label class="form-check-label"
                                                                            for="isIntruder{{ $answer->id }}">
                                                                            Intrus
                                                                        </label>
                                                                    </div>
                                                                    {{-- <div class="form-check">
                                                                        <input class="form-check-input" type="radio"
                                                                            name="is_correct" value="1"
                                                                            id="isNormal{{ $answer->id }}"
                                                                            @if ($answer->is_correct) checked @endif>

                                                                        <label class="form-check-label"
                                                                            for="isNormal{{ $answer->id }}">
                                                                            Normal
                                                                        </label>
                                                                    </div> --}}
                                                                @else
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
                                                                @endif
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
                                @else
                                    <div class="alert alert-warning">
                                        <i class="bi bi-exclamation-triangle"></i> Aucune réponse définie
                                    </div>
                                @endif
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
                                            <div class="mb-3">
                                                <label for="questionType{{ $question->id }}" class="form-label">Type de question</label>
                                                <select id="questionType{{ $question->id }}" class="form-select" name="type">
                                                    <option value="single_choice" {{ $question->type === 'single_choice' ? 'selected' : '' }}>Choix unique</option>
                                                    <option value="multiple_choice" {{ $question->type === 'multiple_choice' ? 'selected' : '' }}>Choix multiple</option>
                                                    <option value="text" {{ $question->type === 'text' ? 'selected' : '' }}>Texte</option>
                                                    <option value="find_intruder" {{ $question->type === 'find_intruder' ? 'selected' : '' }}>Trouver l'intrus</option>
                                                </select>
                                                <small class="text-muted">
                                                    @if($question->type === 'text')
                                                        <i class="bi bi-info-circle"></i> Attention : en changeant de type, vous devrez définir des réponses.
                                                    @elseif(in_array($question->type, ['single_choice', 'multiple_choice', 'find_intruder']))
                                                        <i class="bi bi-info-circle"></i> Attention : en changeant vers "Texte", les réponses existantes seront conservées mais non utilisées.
                                                    @endif
                                                </small>
                                            </div>
                                            <div class="mb-3">
                                                <label for="questionPoints{{ $question->id }}" class="form-label">Points pour cette question</label>
                                                <input type="number" class="form-control"
                                                    id="questionPoints{{ $question->id }}" name="points"
                                                    value="{{ $question->points }}" min="1" max="100">
                                                <small class="text-muted">(Nombre de points attribués à cette question)</small>
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
