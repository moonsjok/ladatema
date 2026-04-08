@extends('layouts.authenticated.owners.index')

@section('page-title', 'Modifier un média - ' . $media->getCustomProperty('name', $media->name))

@section('dashboard-content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-pencil"></i>
                        Modifier le média
                    </h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('media.index', $type) }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Retour aux {{ ucfirst($type) }}
                        </a>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-house"></i> Dashboard
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Informations du média actuel -->
                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="bi bi-info-circle"></i>
                            Média actuel
                        </h6>
                        <div class="row">
                            <div class="col-md-8">
                                <strong>Nom:</strong> {{ $media->getCustomProperty('name', $media->name) }}<br>
                                <strong>Type:</strong> {{ ucfirst($type) }}<br>
                                <strong>Taille:</strong> {{ number_format($media->size / 1024, 2) }} KB<br>
                                <strong>URL:</strong> <a href="{{ route('media.file.secure', $media->id) }}"
                                    target="_blank">{{ route('media.file.secure', $media->id) }}</a>
                                @if ($media->getCustomProperty('description'))
                                    <br><strong>Description:</strong> {{ $media->getCustomProperty('description') }}
                                @endif
                                <br><strong>Visibilité:</strong>
                                @if ($media->getCustomProperty('is_public', false))
                                    <span class="badge bg-success">Public</span>
                                @else
                                    <span class="badge bg-warning">Privé</span>
                                @endif
                            </div>
                            <div class="col-md-4 text-center">
                                @if ($type === 'images')
                                    <img src="{{ route('media.file.secure', $media->id) }}" alt="{{ $media->name }}"
                                        class="img-thumbnail" style=".max-width: 100px; max-height: 200px;">
                                @elseif($type === 'videos')
                                    <video class="img-thumbnail" style=".max-width: 100px; max-height: 200px;" controls>
                                        <source src="{{ route('media.file.secure', $media->id) }}" type="video/mp4">
                                    </video>
                                @else
                                    <i class="bi bi-file-text fs-1 text-secondary"></i>
                                @endif
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('media.update', [$type, $media->id]) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Métadonnées -->
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                <i class="bi bi-tag"></i>
                                Nom du média
                            </label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" required
                                value="{{ old('name', $media->getCustomProperty('name', $media->name)) }}">
                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">
                                <i class="bi bi-text-paragraph"></i>
                                Description (optionnel)
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                rows="3">{{ old('description', $media->getCustomProperty('description', '')) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input @error('is_public') is-invalid @enderror" type="checkbox"
                                    id="is_public" name="is_public" value="1"
                                    @if ($media->getCustomProperty('is_public', false)) checked @endif>
                                <label class="form-check-label" for="is_public">
                                    <i class="bi bi-globe"></i>
                                    Rendre ce média public
                                </label>
                                <div class="form-text">
                                    Les médias publics peuvent être vus par tous les utilisateurs connectés.
                                </div>
                                @error('is_public')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <label for="file" class="form-label">
                                <i class="bi bi-upload"></i>
                                Remplacer le fichier (optionnel)
                                <small class="text-muted">
                                    @if ($type === 'images')
                                        (Formats: jpg, jpeg, png, gif, webp - Max: 10MB)
                                    @elseif($type === 'videos')
                                        (Formats: mp4, webm - Max: 2GB)
                                    @else
                                        (Formats: pdf, txt, md - Max: 10MB)
                                    @endif
                                </small>
                            </label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror" id="file"
                                name="file">
                            @error('file')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Aperçu du nouveau fichier -->
                        @if ($type === 'images')
                            <div class="mb-3" id="newImagePreview" style="display: none;">
                                <label class="form-label">Aperçu du nouveau fichier:</label>
                                <div class="border rounded p-3">
                                    <div class="row">
                                        <div class="col-md-6 text-center">
                                            <h6>Nouveau:</h6>
                                            <img id="newPreviewImg" src="#" alt="Nouveau fichier"
                                                class="img-thumbnail" style="max-height: 150px;">
                                        </div>
                                        <div class="col-md-6 text-center">
                                            <h6>Actuel:</h6>
                                            <img src="{{ route('media.file.secure', $media->id) }}" alt="Fichier actuel"
                                                class="img-thumbnail" style="max-height: 150px;">
                                        </div>
                                    </div>
                                    <div class="mt-2 text-center">
                                        <small class="text-muted" id="newImageInfo"></small>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Informations pour les vidéos -->
                        @if ($type === 'videos')
                            <div class="mb-3" id="newVideoInfo" style="display: none;">
                                <label class="form-label">Informations du nouveau fichier:</label>
                                <div class="border rounded p-3">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-play-circle fs-3 text-primary me-3"></i>
                                        <div>
                                            <div class="fw-bold" id="newVideoName"></div>
                                            <small class="text-muted" id="newVideoSize"></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($media->getCustomProperty('is_public', false))
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle"></i>
                                <strong>Attention:</strong> Ce média est public. Si vous le rendez privé, seuls vous pourrez
                                y accéder.
                            </div>
                        @endif

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('media.index', $type) }}" class="btn btn-secondary">
                                <i class="bi bi-x"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-save"></i>
                                Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-info-circle"></i>
                        Modification de média
                    </h6>
                </div>
                <div class="card-body">
                    <h6>Ce que vous pouvez modifier:</h6>
                    <ul class="small">
                        <li><strong>Nom</strong>: Change le nom affiché</li>
                        <li><strong>Description</strong>: Ajoute une description</li>
                        <li><strong>Visibilité</strong>: Public ou privé</li>
                        <li><strong>Fichier</strong>: Remplacer le fichier (optionnel)</li>
                    </ul>

                    <hr>

                    <h6>Impact des modifications:</h6>
                    <ul class="small">
                        <li>Les métadonnées sont mises à jour immédiatement</li>
                        <li>Le remplacement du fichier conserve l'ID</li>
                        <li>L'URL sécurisée reste identique</li>
                        <li>Les permissions sont respectées</li>
                    </ul>

                    <div class="alert alert-info small">
                        <i class="bi bi-lightbulb"></i>
                        <strong>Conseil:</strong> Vous pouvez modifier uniquement les métadonnées sans remplacer le fichier.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @if ($type === 'images')
        <script>
            document.getElementById('file').addEventListener('change', function(e) {
                const file = e.target.files[0];
                const preview = document.getElementById('newImagePreview');
                const previewImg = document.getElementById('newPreviewImg');
                const imageInfo = document.getElementById('newImageInfo');

                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        imageInfo.textContent = `${file.name} - ${(file.size / 1024).toFixed(2)} KB`;
                        preview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                } else {
                    preview.style.display = 'none';
                }
            });
        </script>
    @elseif($type === 'videos')
        <script>
            document.getElementById('file').addEventListener('change', function(e) {
                const file = e.target.files[0];
                const videoInfo = document.getElementById('newVideoInfo');
                const videoName = document.getElementById('newVideoName');
                const videoSize = document.getElementById('newVideoSize');

                if (file) {
                    videoName.textContent = file.name;
                    videoSize.textContent = `Taille: ${(file.size / 1024 / 1024).toFixed(2)} MB`;
                    videoInfo.style.display = 'block';
                } else {
                    videoInfo.style.display = 'none';
                }
            });
        </script>
    @endif

    <script>
        // Fonction pour copier l'URL dans le presse-papiers
        function copyUrl(url, fileName) {
            navigator.clipboard.writeText(url).then(function() {
                // Afficher une notification de succès
                var toast = '<div class="position-fixed top-0 end-0 p-3" style="z-index: 11">' +
                    '<div class="toast show" role="alert">' +
                    '<div class="toast-header">' +
                    '<i class="bi bi-clipboard-check text-success me-2"></i>' +
                    '<strong class="me-auto">URL copiée!</strong>' +
                    '<button type="button" class="btn-close" data-bs-dismiss="toast"></button>' +
                    '</div>' +
                    '<div class="toast-body">' +
                    'L\'URL de "' + fileName + '" a été copiée dans le presse-papiers.' +
                    '</div>' +
                    '</div>' +
                    '</div>';

                $('body').append(toast);

                // Supprimer le toast après 3 secondes
                setTimeout(function() {
                    $('.toast').parent().remove();
                }, 3000);
            }).catch(function(err) {
                console.error('Erreur lors de la copie: ', err);
                alert('Erreur lors de la copie de l\'URL');
            });
        }
    </script>
@endpush
