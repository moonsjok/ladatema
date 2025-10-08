<div x-data="{ showCreateForm: @entangle('isCreateFormVisible'), showEditForm: @entangle('isEditFormVisible') }">
    <!-- Sélection de la formation -->
    <select wire:model="selectedFormation" wire:change="selectFormation($event.target.value)" class="form-control mb-3">
        <option value="">Choisir une formation</option>
        @foreach ($formations as $formation)
            <option value="{{ $formation->id }}">{{ $formation->title }}</option>
        @endforeach
    </select>

    @if ($selectedFormation)
        <!-- Bouton Ajouter un cours -->
        <button wire:click="showCreateForm" class="btn btn-primary mb-3">
            <i class="bi bi-plus-circle"></i> Ajouter un cours
        </button>

        <!-- Formulaire d'ajout -->
        <div x-show="showCreateForm" x-transition class="bg-light p-4 rounded shadow-sm">
            <h3>Ajouter un cours</h3>
            <form wire:submit.prevent="addCourse">
                <input type="hidden" wire:model="formation_id">
                <input type="text" wire:model="title" placeholder="Titre du cours" class="form-control mt-2"
                    required>
                <textarea wire:model="description" placeholder="Description du cours" class="form-control mt-2"></textarea>
                <button type="submit" class="btn btn-success mt-2">Ajouter</button>
                <button type="button" wire:click="hideForm" class="btn btn-secondary mt-2">Annuler</button>
            </form>
        </div>

        <!-- Formulaire de modification -->
        <div x-show="showEditForm" x-transition class="bg-light p-4 rounded shadow-sm">
            <h3>Modifier le cours</h3>
            <form wire:submit.prevent="updateCourse">
                <input type="hidden" wire:model="formation_id">
                <input type="text" wire:model="title" placeholder="Titre du cours" class="form-control mt-2"
                    required>
                <textarea wire:model="description" placeholder="Description du cours" class="form-control mt-2"></textarea>
                <button type="submit" class="btn btn-success mt-2">Mettre à jour</button>
                <button type="button" wire:click="hideForm" class="btn btn-secondary mt-2">Annuler</button>
            </form>
        </div>

        <!-- Liste des cours -->
        <div class="mt-3">
            <h3>Liste des cours <i class="bi bi-book"></i></h3>
            <table class="table table-striped" id="dataTable">
                <thead>
                    <tr>
                        <th>Titre du cours <i class="bi bi-journal-text"></i></th>
                        <th>Chapitre(s) <i class="bi bi-book-half"></i></th>
                        <th>Actions <i class="bi bi-gear-fill"></i></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($courses as $course)
                        <tr>
                            <td>{{ $course->title }}</td>
                            <td>{{ $course->chapters->count() }}</td>
                            <td>
                                <button wire:click="showEditForm({{ $course->id }})" class="btn btn-warning btn-sm"
                                    title="Modifier">
                                    <i class="bi bi-pencil-square"></i> Modifier
                                </button>
                                <button wire:click="deleteCourse({{ $course->id }})" class="btn btn-danger btn-sm"
                                    title="Supprimer">
                                    <i class="bi bi-trash"></i> Supprimer
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="mt-3">Veuillez sélectionner une formation pour voir ou ajouter des cours.</p>
    @endif
</div>
