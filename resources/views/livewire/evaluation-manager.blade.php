<div>
    <h3><i class="bi bi-clipboard-check"></i> Gestion des évaluations</h3>
    
    <!-- Formulaire d'ajout de question -->
    <div class="card mb-4">
        <div class="card-header">
            <h5><i class="bi bi-plus-circle"></i> Ajouter une question</h5>
        </div>
        <div class="card-body">
            <form wire:submit.prevent="addQuestion">
                <!-- Type de question et points -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Type de question</label>
                        <select class="form-select" wire:model="newQuestion.type">
                            @foreach($questionTypes as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Points</label>
                        <input type="number" class="form-control" wire:model="newQuestion.points" min="1" value="1">
                    </div>
                </div>

                <!-- Texte de la question -->
                <div class="mb-3">
                    <label class="form-label">Question</label>
                    <textarea class="form-control" wire:model="newQuestion.text" rows="3" 
                              placeholder="Entrez votre question ici..."></textarea>
                    @error('newQuestion.text')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Gestion des réponses (sauf pour les questions texte) -->
                @if(!in_array($newQuestion['type'], ['text']))
                    <div class="mb-3">
                        <label class="form-label">
                            Réponses 
                            @if($newQuestion['type'] === 'find_intruder')
                                <small class="text-muted">(Une seule réponse sera l'intrus)</small>
                            @elseif($newQuestion['type'] === 'single_choice')
                                <small class="text-muted">(Une seule réponse correcte)</small>
                            @else
                                <small class="text-muted">(Plusieurs réponses correctes possibles)</small>
                            @endif
                        </label>
                        
                        <!-- Ajout rapide de réponse -->
                        <div class="input-group mb-2">
                            <input type="text" class="form-control" wire:model="answerInput" 
                                   placeholder="Entrez une réponse..." 
                                   wire:keydown.enter="addAnswer">
                            <button type="button" class="btn btn-outline-primary" wire:click="addAnswer">
                                <i class="bi bi-plus"></i> Ajouter
                            </button>
                        </div>

                        <!-- Liste des réponses -->
                        @if(count($newAnswers) > 0)
                            <div class="list-group mb-2">
                                @foreach($newAnswers as $index => $answer)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center flex-grow-1">
                                            <span class="me-3 fw-bold">{{ chr(65 + $index) }})</span>
                                            <span class="flex-grow-1">{{ $answer['text'] }}</span>
                                            
                                            @if($newQuestion['type'] === 'find_intruder')
                                                <span class="badge me-2 {{ $answer['is_correct'] ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $answer['is_correct'] ? 'Normal' : 'Intrus' }}
                                                </span>
                                            @else
                                                <span class="badge me-2 {{ $answer['is_correct'] ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $answer['is_correct'] ? 'Correct' : 'Incorrect' }}
                                                </span>
                                            @endif
                                        </div>
                                        
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    wire:click="toggleCorrectAnswer({{ $index }})"
                                                    title="{{ $newQuestion['type'] === 'find_intruder' ? 'Définir comme intrus' : 'Marquer comme correct' }}">
                                                <i class="bi bi-{{ $newQuestion['type'] === 'find_intruder' ? 'target' : 'check-circle' }}"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    wire:click="removeAnswer({{ $index }})" title="Supprimer">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        
                        @error('newAnswers')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                @endif

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Ajouter la question
                </button>
            </form>
        </div>
    </div>

    <!-- Liste des questions existantes -->
    <div class="card">
        <div class="card-header">
            <h5><i class="bi bi-list-ul"></i> Questions existantes ({{ count($questions) }})</h5>
        </div>
        <div class="card-body">
            @if(count($questions) > 0)
                <div class="accordion" id="questionsAccordion">
                    @foreach($questions as $index => $question)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading{{ $index }}">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                        data-bs-target="#collapse{{ $index }}">
                                    <div class="d-flex justify-content-between align-items-center w-100">
                                        <div>
                                            <span class="badge bg-primary me-2">{{ $index + 1 }}</span>
                                            <strong>{{ $question['question_text'] }}</strong>
                                            <small class="text-muted ms-2">({{ $questionTypes[$question['type']] ?? $question['type'] }})</small>
                                            <small class="text-muted ms-2">{{ $question['points'] }} points</small>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                wire:click="deleteQuestion({{ $question['id'] }})"
                                                onclick="return confirm('Supprimer cette question ?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapse{{ $index }}" class="accordion-collapse collapse" 
                                 data-bs-parent="#questionsAccordion">
                                <div class="accordion-body">
                                    @if($question['type'] === 'text')
                                        <div class="alert alert-info">
                                            <i class="bi bi-info-circle"></i> Question de type texte - réponse libre de l'étudiant
                                        </div>
                                    @elseif(isset($question['answers']) && count($question['answers']) > 0)
                                        <div class="row">
                                            @foreach($question['answers'] as $answer)
                                                <div class="col-md-6 mb-2">
                                                    <div class="card">
                                                        <div class="card-body py-2">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <span>{{ $answer['answer_text'] }}</span>
                                                                <span class="badge {{ $answer['is_correct'] ? 'bg-success' : 'bg-secondary' }}">
                                                                    {{ $question['type'] === 'find_intruder' 
                                                                        ? ($answer['is_correct'] ? 'Normal' : 'Intrus') 
                                                                        : ($answer['is_correct'] ? 'Correct' : 'Incorrect') 
                                                                    }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="alert alert-warning">
                                            <i class="bi bi-exclamation-triangle"></i> Aucune réponse définie
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center text-muted py-4">
                    <i class="bi bi-inbox fs-1"></i>
                    <p class="mt-2">Aucune question ajoutée pour le moment</p>
                </div>
            @endif
        </div>
    </div>
</div>
