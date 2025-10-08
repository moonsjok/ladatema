<span>
<!-- Bouton Ajouter / Masquer -->
<div class="mb-3 text-start">
    <button type="button" class="btn btn-primary" wire:click="toggleForm">
        @if ($showForm)
            <i class="bi bi-x-circle"></i> Masquer le formulaire d'ajout de nouvelle question
        @else
            <i class="bi bi-plus-circle"></i> Ajouter une nouvelle question
        @endif
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
                    <input type="text" id="questionText" class="form-control" wire:model="questionText">
                    @error('questionText') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <!-- Sélection du type de question -->
                <div class="mb-3">
                    <label for="type" class="form-label">Type de question</label>
                    <select id="type" class="form-select" wire:model="type">
                        <option value="text">Texte</option>
                        <option value="multiple_choice">Choix multiple</option>
                    </select>
                    @error('type') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <!-- Réponses dynamiques -->
                <div>
                    <h5>Réponses</h5>
                    @foreach($answers as $index => $answer)
                        <div class="mb-3 d-flex align-items-center">
                            <input type="text" class="form-control me-2" placeholder="Texte de la réponse" wire:model="answers.{{ $index }}.answer_text">
                            <div class="form-check me-2">
                                <input type="checkbox" class="form-check-input" id="isCorrect{{ $index }}" wire:model="answers.{{ $index }}.is_correct">
                                <label class="form-check-label" for="isCorrect{{ $index }}">Correct</label>
                            </div>
                            <button type="button" class="btn btn-danger" wire:click="removeAnswer({{ $index }})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                        @error("answers.$index.answer_text") <span class="text-danger">{{ $message }}</span> @enderror
                    @endforeach
                    <button type="button" class="btn btn-primary mt-2" wire:click="addAnswer">
                        <i class="bi bi-plus"></i> Ajouter une réponse
                    </button>
                </div>

                <!-- Boutons de soumission et annulation -->
                <div class="mt-4">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save"></i> Enregistrer
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