<span>
<!-- Bouton Ajouter / Masquer -->
<div class="mb-3 text-start">
    <button type="button" class="btn btn-primary" wire:click="toggleForm" wire:loading.attr="disabled">
        @if ($showForm)
            <i class="bi bi-x-circle"></i> Masquer le formulaire d'ajout de nouvelle question
        @else
            <i class="bi bi-plus-circle"></i> Ajouter une nouvelle question
        @endif
        <span wire:loading>
            <i class="bi bi-hourglass-split"></i> Chargement...
        </span>
    </button>
</div>

<!-- Card contenant le formulaire -->
@if ($showForm)
    <div class="card">
        <div class="card-header">
            <h5>Gestion des Questions</h5>
        </div>

        <div class="card-body">
            <form wire:submit.prevent="save">
                <!-- Champ pour le texte de la question -->
                <div class="mb-3">
                    <label for="questionText" class="form-label">Texte de la question</label>
                    <input type="text" id="questionText" class="form-control" wire:model.live="questionText">
                    @error('questionText') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <!-- Sélection du type de question -->
                <div class="mb-3">
                    <label for="type" class="form-label">Type de question</label>
                    <select id="type" class="form-select" wire:model.live="type">
                     <option value="single_choice">Choix unique</option>
                    <option value="multiple_choice">Choix multiple</option>
                        <option value="text">Texte</option>
                        <option value="find_intruder">Trouver l'intrus</option>
                    </select>

                    @error('type') <span class="text-danger">{{ $message }}</span> @enderror
                    @error('multiple_choice_correct') <span class="text-danger">{{ $message }}</span> @enderror
                    
                    <!-- Debug pour voir la valeur actuelle -->
                    <div class="mt-2">
                        <small class="text-muted">Type actuel: {{ $type }}</small>
                    </div>
                </div>

                <!-- Points pour la question -->
                <div class="mb-3">
                    <label for="points" class="form-label">Points pour cette question</label>
                    <input type="number" id="points" class="form-control" wire:model.live="points" min="1" max="100">
                    <small class="text-muted">(Nombre de points attribués à cette question)</small>
                </div>

                <!-- Réponses dynamiques -->
                <div>
                    <h5>
                        Réponses
                        @if($type === 'find_intruder')
                            <small class="text-muted">(Une seule réponse sera l'intrus)</small>
                        @elseif($type === 'single_choice')
                            <small class="text-muted">(Une seule réponse correcte)</small>
                        @elseif($type === 'multiple_choice')
                            <small class="text-muted">(Plusieurs réponses correctes possibles)</small>
                        @else
                            <small class="text-muted">(Réponse libre de l'étudiant)</small>
                        @endif
                    </h5>
                    
                    @if($type !== 'text')
                        @foreach($answers as $index => $answer)
                            <div class="mb-3">
                                <!-- Ligne principale : numérotation, texte, checkbox/radio, suppression -->
                                <div class="d-flex align-items-center mb-2">
                                    <span class="me-2 fw-bold">{{ chr(65 + $index) }})</span>
                                    <input type="text" class="form-control me-2" placeholder="Texte de la réponse" wire:model.live="answers.{{ $index }}.answer_text">
                                    
                                    @if($type === 'single_choice')
                                        <!-- Radio buttons pour choix unique -->
                                        <div class="form-check me-2">
                                            <input type="radio" class="form-check-input" name="correct_answer_{{ $index }}{{ $type }}" id="isCorrect{{ $index }}{{ $type }}" 
                                                   value="{{ $index }}" wire:model.live="correctAnswerIndex">
                                            <label class="form-check-label" for="isCorrect{{ $index }}{{ $type }}">Correct</label>
                                        </div>
                                    @elseif($type === 'find_intruder')
                                        <!-- Radio buttons pour trouver l'intrus -->
                                        <div class="form-check me-2">
                                            <input type="radio" class="form-check-input" name="intruder_answer_{{ $index }}{{ $type }}" id="isIntruder{{ $index }}{{ $type }}" 
                                                   value="{{ $index }}" wire:model.live="intruderAnswerIndex">
                                            <label class="form-check-label" for="isIntruder{{ $index }}{{ $type }}">Intrus</label>
                                        </div>
                                    @elseif($type === 'multiple_choice')
                                        <!-- Checkboxes pour choix multiple -->
                                        <div class="form-check me-2">
                                            <input type="checkbox" class="form-check-input" id="isCorrect{{ $index }}{{ $type }}" 
                                                   wire:model.live="answers.{{ $index }}.is_correct">
                                            <label class="form-check-label" for="isCorrect{{ $index }}{{ $type }}">Correct</label>
                                        </div>
                                    @else
                                        <!-- Par défaut, checkboxes pour les autres types -->
                                        <div class="form-check me-2">
                                            <input type="checkbox" class="form-check-input" id="isCorrect{{ $index }}{{ $type }}" 
                                                   wire:model.live="answers.{{ $index }}.is_correct">
                                            <label class="form-check-label" for="isCorrect{{ $index }}{{ $type }}">Correct</label>
                                        </div>
                                    @endif
                                    
                                    <button type="button" class="btn btn-danger" wire:click="removeAnswer({{ $index }})" wire:loading.attr="disabled">
                                        <span wire:target="removeAnswer({{ $index }})" wire:loading>
                                            <i class="bi bi-hourglass-split"></i>
                                        </span>
                                        <span wire:target="removeAnswer({{ $index }})" wire:loading.remove>
                                            <i class="bi bi-trash"></i>
                                        </span>
                                    </button>
                                </div>
                                
                                <!-- Ligne d'explication sur toute la largeur -->
                                <div class="ms-4">
                                    <textarea class="form-control" placeholder="Explication (optionnelle)" rows="2" wire:model.live="answers.{{ $index }}.explanation"></textarea>
                                </div>
                                
                                @error("answers.$index.answer_text") <span class="text-danger">{{ $message }}</span> @enderror
                                @error("answers.$index.explanation") <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        @endforeach
                        <button type="button" class="btn btn-primary mt-2" wire:click="addAnswer" wire:loading.attr="disabled">
                            <span wire:target="addAnswer" wire:loading>
                                <i class="bi bi-hourglass-split"></i> Ajout en cours...
                            </span>
                            <span wire:target="addAnswer" wire:loading.remove>
                                <i class="bi bi-plus"></i> Ajouter une réponse
                            </span>
                        </button>
                    @else
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Pour les questions de type texte, les étudiants répondront librement sans réponses prédéfinies.
                        </div>
                    @endif
                </div>

                <!-- Boutons de soumission et annulation -->
                <div class="mt-4">
                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                        <span wire:target="save" wire:loading>
                            <i class="bi bi-hourglass-split"></i> Enregistrement en cours...
                        </span>
                        <span wire:target="save" wire:loading.remove>
                            <i class="bi bi-check-circle"></i> Enregistrer la question
                        </span>
                    </button>
                    <button type="button" class="btn btn-secondary" wire:click="toggleForm">
                        <i class="bi bi-x-circle"></i> Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif

</span>