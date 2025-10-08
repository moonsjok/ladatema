<div>
    <!-- En-tête du cours -->
    <div class="bg-primary text-white py-4">
        <div class="container">
            <h1 class="mb-3">{{ $selectedCourse->title }}</h1>
            <hr />
            <p class="lead text-justify">{!! $selectedCourse->description !!}</p>
        </div>
    </div>

    <!-- Navigation entre les cours -->
    <div class="d-flex justify-content-between mt-3">
        @if ($previousCourse)
            <button class="btn btn-outline-primary" wire:click="goToPreviousCourse">
                <i class="bi bi-arrow-left-circle me-2"></i>Cours Précédent
            </button>
        @endif
        @if ($nextCourse)
            <button class="btn btn-outline-primary" wire:click="goToNextCourse">
                Cours Suivant <i class="bi bi-arrow-right-circle ms-2"></i>
            </button>
        @endif
    </div>

    <div class="container my-5">
        <div class="row">
            <!-- Liste des chapitres du cours -->
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-list-ol me-2"></i>Chapitres
                        </h5>
                    </div>
                    <div class="list-group list-group-flush">
                        @if ($chapters->isEmpty())
                            <div class="list-group-item text-center text-muted py-3">
                                Aucun chapitre disponible pour ce cours.
                            </div>
                        @else
                            @foreach ($chapters as $chapter)
                                <button wire:click="selectChapter({{ $chapter->id }})"
                                    class="list-group-item list-group-item-action {{ $selectedChapter?->id === $chapter->id ? 'active' : '' }}">
                                    <i class="bi bi-file-earmark-text me-2"></i>
                                    {{ $chapter->numero }} - {{ $chapter->title }}
                                </button>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <!-- Contenu du chapitre sélectionné -->
            <div class="col-md-8">
                @if ($chapters->isNotEmpty() && $selectedChapter)
                    <!-- Navigation entre les chapitres -->
                    <div class="d-flex justify-content-between mb-3">
                        <button class="btn btn-outline-secondary" wire:click="previousChapter"
                            {{ $selectedChapter->numero == $chapters->min('numero') ? 'disabled' : '' }}>
                            <i class="bi bi-arrow-left-circle me-2"></i>Chapitre Précédent
                        </button>
                        <button class="btn btn-outline-secondary" wire:click="nextChapter"
                            {{ $selectedChapter->numero == $chapters->max('numero') ? 'disabled' : '' }}>
                            Chapitre Suivant <i class="bi bi-arrow-right-circle ms-2"></i>
                        </button>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-file-earmark-text me-2"></i>
                                {{ $selectedChapter->numero }} - {{ $selectedChapter->title }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <p>{!! $selectedChapter->content !!}</p>
                        </div>
                    </div>

                    <!-- Navigation entre les chapitres (répétée) -->
                    <div class="d-flex justify-content-between mt-3">
                        <button class="btn btn-outline-secondary" wire:click="previousChapter"
                            {{ $selectedChapter->numero == $chapters->min('numero') ? 'disabled' : '' }}>
                            <i class="bi bi-arrow-left-circle me-2"></i>Chapitre Précédent
                        </button>
                        <button class="btn btn-outline-secondary" wire:click="nextChapter"
                            {{ $selectedChapter->numero == $chapters->max('numero') ? 'disabled' : '' }}>
                            Chapitre Suivant <i class="bi bi-arrow-right-circle ms-2"></i>
                        </button>
                    </div>
                @else
                    <div class="alert alert-warning text-center">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        Aucun chapitre sélectionné ou disponible pour ce cours.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Navigation entre les cours (répétée) -->
    <div class="d-flex justify-content-between mt-3">
        @if ($previousCourse)
            <button class="btn btn-outline-primary" wire:click="goToPreviousCourse">
                <i class="bi bi-arrow-left-circle me-2"></i>Cours Précédent
            </button>
        @endif
        @if ($nextCourse)
            <button class="btn btn-outline-primary" wire:click="goToNextCourse">
                Cours Suivant <i class="bi bi-arrow-right-circle ms-2"></i>
            </button>
        @endif
    </div>
</div>
