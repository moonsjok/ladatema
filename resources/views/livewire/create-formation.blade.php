<div class="container mt-4">
    <h2 class="text-center">Créer rapidement une formation</h2>
    <h2 class="text-center">Étape {{ $step }}</h2>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    @if ($step == 1)
                        <h2 class="card-title">Catégorie</h2>
                        <div class="mb-3">
                            <label for="categorySelect" class="form-label">Sélectionner une catégorie</label>
                            <select wire:model="category_id" wire:change="selectCategory($event.target.value)"
                                id="categorySelect" class="form-select">
                                <option value="">Choisir une catégorie</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="newCategoryName" class="form-label">Ou créer une nouvelle catégorie</label>
                            <input type="text" wire:model="category_name" id="newCategoryName"
                                class="form-control @error('category_name') is-invalid @enderror"
                                placeholder="Nom de la nouvelle catégorie">
                            @error('category_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <input type="text" wire:model="category_description"
                                class="form-control mt-2 @error('category_description') is-invalid @enderror"
                                placeholder="Description de la nouvelle catégorie">
                            @error('category_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <button wire:click="saveCategory" class="btn btn-secondary mt-2">Créer la catégorie</button>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            <button wire:click="saveCategory" class="btn btn-primary">Suivant</button>
                        </div>
                    @elseif ($step == 2)
                        <h2 class="card-title">Sous-catégorie</h2>
                        <div class="mb-3">
                            <label for="subcategorySelect" class="form-label">Sélectionner une sous-catégorie</label>
                            <select wire:model="subcategory_id" wire:change="selectSubcategory($event.target.value)"
                                id="subcategorySelect" class="form-select">
                                <option value="">Choisir une sous-catégorie</option>
                                @foreach ($subcategories as $subcategory)
                                    <option value="{{ $subcategory->id }}">{{ $subcategory->name }}</option>
                                @endforeach
                            </select>
                            @error('subcategory_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="newSubcategoryName" class="form-label">Ou créer une nouvelle
                                sous-catégorie</label>
                            <input type="text" wire:model="subcategory_name" id="newSubcategoryName"
                                class="form-control @error('subcategory_name') is-invalid @enderror"
                                placeholder="Nom de la nouvelle sous-catégorie">
                            @error('subcategory_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <input type="text" wire:model="subcategory_description"
                                class="form-control mt-2 @error('subcategory_description') is-invalid @enderror"
                                placeholder="Description de la nouvelle sous-catégorie">
                            @error('subcategory_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <button wire:click="saveSubcategory" class="btn btn-secondary mt-2">Créer la
                                sous-catégorie</button>
                        </div>
                        <div class="d-flex justify-content-between mt-3">
                            <button wire:click="goBack" class="btn btn-secondary">Retour</button>
                            <button wire:click="selectSubcategory()" class="btn btn-primary">Suivant</button>
                        </div>
                    @elseif ($step == 3)
                        <h2 class="card-title">Formation</h2>
                        <form wire:submit.prevent="saveFormation">
                            <div class="mb-3">
                                <label for="formationTitle" class="form-label">Titre de la formation</label>
                                <input type="text" wire:model="formation_title" id="formationTitle"
                                    class="form-control @error('formation_title') is-invalid @enderror"
                                    placeholder="Titre de la formation">
                                @error('formation_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="formationDescription" class="form-label">Description de la formation</label>
                                <textarea wire:model="formation_description" id="formationDescription"
                                    class="form-control @error('formation_description') is-invalid @enderror" placeholder="Description de la formation"></textarea>
                                @error('formation_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="formationPrice" class="form-label">Prix</label>
                                <input type="number" wire:model="formation_price" id="formationPrice"
                                    class="form-control @error('formation_price') is-invalid @enderror"
                                    placeholder="Prix de la formation">
                                @error('formation_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="d-flex justify-content-between mt-3">
                                <button wire:click="goBack" class="btn btn-secondary">Retour</button>
                                <button type="submit" class="btn btn-primary">Suivant</button>
                            </div>
                        </form>
                    @elseif ($step == 4)
                        <h2 class="card-title">Cours et Chapitres</h2>
                        <div class="mb-3">
                            <label for="courseTitle" class="form-label">Titre du cours</label>
                            <input type="text" wire:model="course_title" id="courseTitle"
                                class="form-control @error('course_title') is-invalid @enderror"
                                placeholder="Titre du cours">
                            @error('course_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="courseContent" class="form-label">Contenu du cours</label>
                            <textarea wire:model="course_content" id="courseContent"
                                class="form-control @error('course_content') is-invalid @enderror" placeholder="Contenu du cours"></textarea>
                            @error('course_content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button wire:click="addCourse" class="btn btn-secondary mb-3">Ajouter le cours</button>

                        @foreach ($courses as $index => $course)
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Cours: {{ $course['title'] }}</h5>
                                    <div class="mb-3">
                                        <label for="chapterTitle{{ $index }}" class="form-label">Titre du
                                            chapitre</label>
                                        <input type="text" wire:model="chapter_title"
                                            id="chapterTitle{{ $index }}"
                                            class="form-control @error('chapter_title') is-invalid @enderror"
                                            placeholder="Titre du chapitre">
                                        @error('chapter_title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="chapterContent{{ $index }}" class="form-label">Contenu du
                                            chapitre</label>
                                        <textarea wire:model="chapter_content" id="chapterContent{{ $index }}"
                                            class="form-control @error('chapter_content') is-invalid @enderror" placeholder="Contenu du chapitre"></textarea>
                                        @error('chapter_content')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <button wire:click="addChapter({{ $index }})"
                                        class="btn btn-secondary mb-3">Ajouter le chapitre</button>

                                    @foreach ($course['chapters'] as $chapterIndex => $chapter)
                                        <div class="card mb-2">
                                            <div class="card-body">
                                                <h6 class="card-title">Chapitre: {{ $chapter['title'] }}</h6>
                                                <p class="card-text">{{ $chapter['content'] }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        <div class="d-flex justify-content-between mt-3">
                            <button wire:click="goBack" class="btn btn-secondary">Retour</button>
                            <button wire:click="saveAll" class="btn btn-primary">Enregistrer tout</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        window.addEventListener('swal', event => {
            Swal.fire({
                icon: event.detail.icon,
                title: event.detail.title,
                text: event.detail.text
            });
        });
    </script>
@endpush
