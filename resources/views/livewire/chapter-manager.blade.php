<div x-data="{ showCreateForm: @entangle('isCreateFormVisible'), showEditForm: @entangle('isEditFormVisible') }">
    <!-- Bouton Ajouter un chapitre -->
    <button wire:click="showCreateForm" class="btn btn-primary mb-3">
        <i class="bi bi-plus-circle"></i> Ajouter un chapitre
    </button>

    <!-- Formulaire d'ajout -->
    <div x-show="showCreateForm" x-transition class="bg-light p-4 rounded shadow-sm">
        <h3>Ajouter un chapitre</h3>
        <form wire:submit.prevent="addChapter">
            <select wire:model="course_id" class="form-control" required>
                <option value="">Sélectionner un cours</option>
                @foreach ($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                @endforeach
            </select>
            <input type="text" wire:model="title" placeholder="Titre du chapitre" class="form-control mt-2" required>
            <textarea wire:model="content" placeholder="Contenu du chapitre" class="form-control mt-2"></textarea>
            <button type="submit" class="btn btn-success mt-2">Ajouter</button>
            <button type="button" wire:click="hideForm" class="btn btn-secondary mt-2">Annuler</button>
        </form>
    </div>

    <!-- Formulaire de modification -->
    <div x-show="showEditForm" x-transition class="bg-light p-4 rounded shadow-sm">
        <h3>Modifier le chapitre</h3>
        <form wire:submit.prevent="updateChapter">
            <select wire:model="course_id" class="form-control" required>
                <option value="">Sélectionner un cours</option>
                @foreach ($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                @endforeach
            </select>
            <input type="text" wire:model="title" placeholder="Titre du chapitre" class="form-control mt-2" required>
            <textarea wire:model="content" placeholder="Contenu du chapitre" class="form-control mt-2"></textarea>
            <button type="submit" class="btn btn-success mt-2">Mettre à jour</button>
            <button type="button" wire:click="hideForm" class="btn btn-secondary mt-2">Annuler</button>
        </form>
    </div>

    <!-- Liste des chapitres -->
    <div class="mt-3">
        <h3>Liste des chapitres</h3>
        <table class="table table-striped" id="dataTable">
            <thead>
                <tr>
                    <th>Cours</th>
                    <th>Titre</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($chapters as $chapter)
                    <tr>
                        <td>{{ $chapter->course->title }}</td>
                        <td>{{ $chapter->title }}</td>
                        <td>
                            <button wire:click="showEditForm({{ $chapter->id }})" class="btn btn-warning btn-sm">
                                Modifier
                            </button>
                            <button wire:click="deleteChapter({{ $chapter->id }})" class="btn btn-danger btn-sm">
                                Supprimer
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
