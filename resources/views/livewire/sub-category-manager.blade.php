<div x-data="{ showCreateForm: @entangle('isCreateFormVisible'), showEditForm: @entangle('isEditFormVisible') }">
    <!-- Sélection de la catégorie -->
    <select wire:model="selectedCategory" wire:change="selectCategory($event.target.value)" class="form-control mb-3">
        <option value="">Choisir une catégorie</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}">{{ $category->name }}</option>
        @endforeach
    </select>

    @if ($selectedCategory)
        <button wire:click="clearSelection" class="btn btn-secondary mb-3">
            <i class="bi bi-arrow-left-circle"></i> Annuler la sélection
        </button>

        <!-- Bouton pour afficher le formulaire d'ajout -->
        <button wire:click="showCreateForm" class="btn btn-primary mb-3" wire:loading.attr="disabled">
            <span wire:loading.remove>
                <i class="bi bi-plus-circle"></i> Ajouter une sous-catégorie
            </span>
            <span wire:loading>
                <i class="spinner-border spinner-border-sm"></i> Chargement...
            </span>
        </button>

        <!-- Formulaire d'ajout -->
        <div x-show="showCreateForm" x-transition:enter="transition-all duration-500 ease-out"
            x-transition:leave="transition-all duration-500 ease-in">
            <div class="bg-light p-4 rounded shadow-sm">
                <h3 class="mt-3">Ajouter une sous-catégorie</h3>
                <form class="mt-3 mb-3" wire:submit.prevent="addSubCategory">
                    <input type="hidden" wire:model="category_id">
                    <input type="text" wire:model="name" placeholder="Nom de la sous-catégorie" class="form-control"
                        required>
                    <textarea wire:model="description" placeholder="Description" class="form-control"></textarea>
                    <br>
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove>
                            <i class="bi bi-check-circle"></i> Ajouter
                        </span>
                        <span wire:loading>
                            <i class="spinner-border spinner-border-sm"></i> En cours...
                        </span>
                    </button>
                    <button wire:click="hideForm" type="button" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Annuler
                    </button>
                </form>
            </div>
        </div>

        <!-- Formulaire de modification -->
        <div x-show="showEditForm" x-transition:enter="transition-all duration-500 ease-out"
            x-transition:leave="transition-all duration-500 ease-in">
            <div class="bg-light p-4 rounded shadow-sm">
                <h3 class="mt-3">Editer la sous-catégorie <strong>{{ $name }}</strong></h3>
                <form wire:submit.prevent="updateSubCategory">
                    <input type="hidden" wire:model="category_id">
                    <input type="text" wire:model="name" placeholder="Nom de la sous-catégorie" class="form-control"
                        required>
                    <textarea wire:model="description" placeholder="Description" class="form-control mb-3"></textarea>

                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-pencil-square"></i> Mettre à jour
                    </button>
                    <button wire:click="hideForm" type="button" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Annuler
                    </button>
                </form>
            </div>
        </div>

        <!-- Liste des sous-catégories -->
        <h2 class="mt-3 mb-3">Liste des sous-catégories</h2>
        <div class="bg-white p-4 rounded shadow-sm">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th class="d-none d-md-table-cell">Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($subCategories as $subCategory)
                        <tr>
                            <td>
                                <strong>{{ $subCategory->name }}</strong>
                            </td>
                            <td class="d-none d-md-table-cell">
                                {!! $subCategory->description !!}
                            </td>
                            <td>
                                <button wire:click="showEditForm({{ $subCategory->id }})" class="btn btn-warning btn-sm"
                                    wire:loading.attr="disabled">
                                    <span wire:loading.remove>
                                        <i class="bi bi-pencil-square"></i> Editer
                                    </span>
                                    <span wire:loading>
                                        <i class="spinner-border spinner-border-sm"></i>
                                    </span>
                                </button>
                                <button wire:click="deleteSubCategory({{ $subCategory->id }})"
                                    class="btn btn-danger btn-sm" wire:loading.attr="disabled">
                                    <span wire:loading.remove>
                                        <i class="bi bi-trash"></i> Supprimer
                                    </span>
                                    <span wire:loading>
                                        <i class="spinner-border spinner-border-sm"></i>
                                    </span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">Aucune sous-catégorie trouvée pour cette catégorie.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @else
        <p class="mt-3">Veuillez sélectionner une catégorie pour voir ou ajouter des sous-catégories.</p>
    @endif
</div>
