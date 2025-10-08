{{-- ressource/views/livewire/guest/formation-list.blade.php --}}
<div>
    <div class="row my-5  mx-xl-5 mx-lg-5  mx-md-2 mx-sm-2 mx-xs-2">
        <!-- Sidebar pour les catégories -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">

                    <!-- Affichage conditionnel pour mobile -->
                    <div class="d-block d-md-none">
                        @if ($selectedCategory)
                            <div class="d-flex align-items-center justify-content-between">
                                <span>
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-outline-secondary w-100" type="button"
                                            wire:click="toggleCategoryList">
                                            <span wire:loading.remove wire:target="toggleCategoryList, selectCategory">
                                                {{ $categories->firstWhere('id', $selectedCategory)->name }}
                                                <i class="bi bi-filter ms-2"></i>
                                            </span>
                                            <span wire:loading wire:target="toggleCategoryList, selectCategory">
                                                <span class="spinner-border spinner-border-sm" role="status"
                                                    aria-hidden="true"></span>
                                                Chargement...
                                            </span>
                                        </button>
                                    </div>
                                </span>
                            </div>
                        @else
                            <p>Sélectionnez une catégorie</p>
                        @endif

                        <!-- Liste complète des catégories, affichée si showCategoryList est true -->
                        @if ($showCategoryList)
                            <ul class="list-group mt-2" wire:loading.class="opacity-50" wire:target="selectCategory">
                                @foreach ($categories as $category)
                                    <li class="list-group-item category-link {{ $selectedCategory == $category->id ? 'active-category' : '' }}"
                                        wire:click="selectCategory({{ $category->id }})" style="cursor: pointer;">
                                        {{ $category->name }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    <!-- Affichage pour desktop (liste complète toujours visible) -->
                    <div class="d-none d-md-block">
                        <h5 class="mb-2">Catégories</h5>
                        <hr />
                        <ul class="list-group">
                            @foreach ($categories as $category)
                                <li class="list-group-item category-link {{ $selectedCategory == $category->id ? 'active-category' : '' }}"
                                    wire:click="selectCategory({{ $category->id }})" style="cursor: pointer;">
                                    {{ $category->name }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- Contenu principal -->
        <div class="col-md-9">
            @if (!$selectedCategory)
                <div class="vh-100 d-flex justify-content-center align-items-center">
                    <div class="card col-6 p-3 bg-light">
                        <div class="text-center">
                            <i class="bi bi-info-circle display-2"></i> <!-- Icône Bootstrap -->
                            <p class="fw-bold">
                                Cliquez sur une catégorie
                                <br />
                                pour afficher ses sous-catégories
                                et formations.
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <!-- Card principale contenant les sous-catégories et formations -->
                <div class="card">
                    <div class="card-body">
                        <!-- Détails de la catégorie -->
                        <h2>{{ $categoryDetails->name }}</h2>
                        <p class="text-muted text-justify">
                            {!! $categoryDetails->description !!}
                        </p>
                        <p class="text-muted">
                            <strong>{{ $subCategories->count() }}</strong> sous-catégorie(s) -
                            <strong>{{ $formations->total() }}</strong> formation(s)
                        </p>

                        <!-- Section des sous-catégories -->
                        <div class="bg-light p-2 mb-3">
                            <strong>Sous-catégories</strong>
                        </div>
                        <div class="mb-4">
                            @foreach ($subCategories as $subCategory)
                                <button class="btn btn-outline-primary btn-sm m-1"
                                    wire:click="selectSubCategory({{ $subCategory->id }})">
                                    {{ $subCategory->name }}
                                </button>
                            @endforeach
                        </div>

                        <!-- Liste des formations -->
                        <div class="bg-light p-2 mb-3">
                            <strong>Formations</strong>
                        </div>

                        <div class="row">
                            @if ($formations->isEmpty())
                                <div class="col-12 d-flex justify-content-center align-items-center"
                                    style="height: 200px;">
                                    <div class="alert alert-warning d-flex align-items-center" role="alert">
                                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                        <div>Aucune formation disponible pour cette sélection.</div>
                                    </div>
                                </div>
                            @else
                                @foreach ($formations as $formation)
                                    <div class="col-md-4 mb-4 d-flex align-items-stretch">
                                        <a href="{{ route('guest.formations.show', ['formation' => $formation->id, 'slug' => Str::slug($formation->title)]) }}"
                                            class="btn btn-light card w-100">
                                            <div class="card-body">
                                                <h5 class="card-title">{{ $formation->title }}</h5>
                                                <p class="card-text">
                                                    {{ Str::limit(strip_tags($formation->description), 130, '...') }}
                                                </p>
                                                <p class="d-flex justify-content-between">
                                                    @if (floatval($formation->price) == 0)
                                                        <strong class="badge text-bg-success display-5">
                                                            Gratuit
                                                            <i class="bi bi-unlock"></i>
                                                        </strong>
                                                    @else
                                                        Prix : {{ $formation->price }}FCFA (XOF)
                                                    @endif
                                                    <strong class="text-primary">{{ $formation->courses->count() }}
                                                        cour(s)</strong>
                                                </p>
                                            </div>
                                            <div class="card-footer pt-2 pb-2 pl-0 pr-0 mr-0 ml-0 ">
                                                <i class="bi bi-info-circle"></i> En savoir plus
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <!-- Bouton "Load More" et indicateur de chargement -->
                        <div class="d-flex justify-content-center mt-4">
                            @if ($formations->hasMorePages())
                                <button class="btn btn-primary" wire:click="loadMore" wire:loading.attr="disabled">
                                    <span wire:loading.remove>Charger plus</span>
                                    <span wire:loading>
                                        <span class="spinner-border spinner-border-sm" role="status"
                                            aria-hidden="true"></span>
                                        Chargement...
                                    </span>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    @push('styles')
        <style>
            .category-link {
                color: #333;
                text-decoration: none;
            }

            .category-link.active-category {
                color: #007BFF;
                font-weight: bold;
            }

            /* Uniformiser la hauteur des cartes */
            .card {
                height: 100%;
                display: flex;
                flex-direction: column;
            }

            .card-body {
                flex-grow: 1;
                display: flex;
                flex-direction: column;
            }

            .card-text {
                flex-grow: 1;
            }

            /* Assurer l'alignement des cartes */
            .align-items-stretch {
                display: flex;
            }

            .category-link {
                color: #333;
                text-decoration: none;
                transition: background-color 0.3s, color 0.3s;
            }

            .category-link:hover,
            .category-link:focus {
                background-color: var(--accent-color);
                color: white;
                font-weight: bold;
            }

            .category-link.active-category {
                background-color: var(--accent-color);
                color: white;
                font-weight: bold;
            }
        </style>
    @endpush


</div>
