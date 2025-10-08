<span>
    <div x-data="{ showCreateForm: @entangle('isCreateFormVisible'), showEditForm: @entangle('isEditFormVisible') }">
        <!-- Bouton pour afficher le formulaire d'ajout -->
        <button wire:click="showCreateForm" class="btn btn-primary mb-3" wire:loading.attr="disabled">
            <span wire:loading.remove>
                <i class="bi bi-plus-circle"></i> Ajouter une catégorie
            </span>
            <span wire:loading>
                <i class="spinner-border spinner-border-sm"></i> Chargement...
            </span>
        </button>

        <!-- Formulaire d'ajout -->
        <div x-show="showCreateForm" x-transition:enter="transition-all duration-500 ease-out"
            x-transition:leave="transition-all duration-500 ease-in">
            <div class="bg-light p-4 rounded shadow-sm">
                <h3 class="mt-3">Ajouter une catégorie</h3>
                <form class="mt-3 mb-3" wire:submit.prevent="addCategory">
                    <input type="text" wire:model="name" placeholder="Nom de la catégorie" class="form-control"
                        required>
                    <textarea wire:model="description" placeholder="Description" class="form-control"></textarea>
                    <br>
                    <!-- Bouton pour ajouter une catégorie -->
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
                <h3 class="mt-3">Editer la catégorie <strong>{{ $name }}</strong></h3>
                <form wire:submit.prevent="updateCategory">
                    <input type="text" wire:model="name" placeholder="Nom de la catégorie" class="form-control"
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
    </div>

    <!-- Liste des catégories -->
    <h2 class="mt-3 mb-3">Liste des catégories</h2>
    <div class="bg-white p-4 rounded shadow-sm">
        <table class="table table-striped " id="dataTable">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th class="d-none d-md-table-cell">Description</th>
                    {{-- <th class="d-none d-md-table-cell">Actions</th> --}}
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $category)
                    <tr>
                        <td>
                            <strong>{{ $category->name }}</strong>
                        </td>
                        <td class="d-none d-md-table-cell">
                            {!! $category->description !!}
                            <!-- Actions sous le titre pour mobile -->
                            <div class="p-3 text-center">
                                <!-- Boutons d'action dans la liste -->
                                <button wire:click="showEditForm({{ $category->id }})" class="btn btn-warning btn-sm"
                                    wire:loading.attr="disabled">
                                    <span wire:loading.remove>
                                        <i class="bi bi-pencil-square"></i> Editer
                                    </span>
                                    <span wire:loading>
                                        <i class="spinner-border spinner-border-sm"></i>
                                    </span>
                                </button>

                                <button wire:click="deleteCategory({{ $category->id }})" class="btn btn-danger btn-sm"
                                    wire:loading.attr="disabled">
                                    <span wire:loading.remove>
                                        <i class="bi bi-trash"></i> Supprimer
                                    </span>
                                    <span wire:loading>
                                        <i class="spinner-border spinner-border-sm"></i>
                                    </span>
                                </button>
                                <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#categoryModal{{ $category->id }}">
                                    <i class="bi bi-eye"></i> Voir
                                </button>
                            </div>
                        </td>

                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal Bootstrap -->
    @foreach ($categories as $category)
        <div class="modal fade" id="categoryModal{{ $category->id }}" tabindex="-1"
            aria-labelledby="categoryModalLabel{{ $category->id }}" aria-hidden="true">
            <div class="modal-dialog  modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="categoryModalLabel{{ $category->id }}">Détails de la catégorie</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Nom :</strong> {{ $category->name }}</p>
                        <p><strong>Description :</strong> {!! $category->description !!}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</span>
