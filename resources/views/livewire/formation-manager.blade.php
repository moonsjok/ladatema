<div x-data="{ showCreateForm: @entangle('isCreateFormVisible'), showEditForm: @entangle('isEditFormVisible') }">
    {{-- <!-- Alertes pour les messages de session -->
    @if (session()->has('alert'))
        <div class="alert alert-{{ session('alert.type') }} mt-3">
            <strong>{{ session('alert.title') }}</strong> - {{ session('alert.message') }}
        </div>
    @endif --}}

    <!-- Sélection de la catégorie -->
    <select wire:model="selectedCategory" wire:change="loadSubCategories($event.target.value)" class="form-control mb-3">
        <option value="">Choisir une catégorie</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}">{{ $category->name }}</option>
        @endforeach
    </select>

    <!-- Sélection de la sous-catégorie -->
    @if ($selectedCategory)
        <select wire:model="selectedSubCategory" wire:change="selectSubCategory($event.target.value)"
            class="form-control mb-3">
            <option value="">Choisir une sous-catégorie</option>
            @foreach ($subCategories as $subCategory)
                <option value="{{ $subCategory->id }}">{{ $subCategory->name }}</option>
            @endforeach
        </select>
    @endif

    @if ($selectedSubCategory)
        <!-- Bouton pour afficher le formulaire de création -->
        <button wire:click="showCreateForm" class="btn btn-primary mb-3">
            <i class="bi bi-plus-circle"></i> Ajouter une formation
        </button>

        <!-- Formulaire d'ajout -->
        <div x-show="showCreateForm" x-transition class="bg-light p-4 rounded shadow-sm">
            <h3>Ajouter une formation</h3>
            <form wire:submit.prevent="addFormation">
                <input type="hidden" wire:model="category_id">
                <input type="hidden" wire:model="sub_category_id">
                <input type="text" wire:model="title" placeholder="Titre de la formation" class="form-control mt-2"
                    required>
                <textarea wire:model="description" placeholder="Description" class="form-control mt-2"></textarea>
                <input type="number" wire:model="price" placeholder="Prix (FCFA - xof)" class="form-control mt-2"
                    required>
                <button type="submit" class="btn btn-success mt-2">Ajouter</button>
                <button type="button" wire:click="hideForm" class="btn btn-secondary mt-2">Annuler</button>
            </form>
        </div>

        <!-- Formulaire de modification -->
        <div x-show="showEditForm" x-transition class="bg-light p-4 rounded shadow-sm">
            <h3>Modifier la formation</h3>
            <form wire:submit.prevent="updateFormation">
                <select wire:model="category_id" class="form-control" required>
                    <option value="">Sélectionner une catégorie</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                <select wire:model="sub_category_id" class="form-control mt-2" required>
                    <option value="">Sélectionner une sous-catégorie</option>
                    @foreach ($subCategories as $subCategory)
                        <option value="{{ $subCategory->id }}">{{ $subCategory->name }}</option>
                    @endforeach
                </select>
                <input type="text" wire:model="title" placeholder="Titre de la formation" class="form-control mt-2"
                    required>
                <textarea wire:model="description" placeholder="Description" class="form-control mt-2"></textarea>
                <input type="number" wire:model="price" placeholder="Prix (FCFA - xof)" class="form-control mt-2"
                    required>
                <button type="submit" class="btn btn-success mt-2">Mettre à jour</button>
                <button type="button" wire:click="hideForm" class="btn btn-secondary mt-2">Annuler</button>
            </form>
        </div>

        <!-- Liste des formations -->
        <div class="mt-3">
            <h3>Liste des formations</h3>
            <table class="table table-striped" id="dataTable">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Catégorie</th>
                        <th>Sous-Catégorie</th>
                        <th>Prix</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($formations as $formation)
                        <tr>
                            <td>{{ $formation->title }}</td>
                            <td>{{ $formation->category->name ?? 'N/A' }}</td>
                            <td>{{ $formation->subcategory->name ?? 'N/A' }}</td>
                            <td>{{ $formation->price }}</td>
                            <td>
                                <button wire:click="showEditForm({{ $formation->id }})" class="btn btn-warning btn-sm">
                                    Modifier
                                </button>
                                <button wire:click="deleteFormation({{ $formation->id }})"
                                    class="btn btn-danger btn-sm">
                                    Supprimer
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="mt-3">Sélectionnez une catégorie et une sous-catégorie pour afficher ou ajouter des formations.</p>
    @endif
</div>
