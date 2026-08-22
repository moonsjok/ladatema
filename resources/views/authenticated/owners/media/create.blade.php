@extends('layouts.authenticated.owners.index')

@section('page-title', 'Ajouter un fichier - ' . ucfirst($type))

@section('dashboard-content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-plus"></i>
                        Ajouter un fichier {{ ucfirst($type) }}
                    </h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('media.index', $type) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour aux {{ ucfirst($type) }}
                        </a>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Navigation entre types de fichiers -->
                    <div class="mb-3">
                        <div class="btn-group" role="group">
                            <a href="{{ route('media.create', 'images') }}"
                                class="btn {{ $type === 'images' ? 'btn-primary' : 'btn-outline-primary' }}">
                                <i class="fas fa-image"></i> Images
                            </a>
                            <a href="{{ route('media.create', 'videos') }}"
                                class="btn {{ $type === 'videos' ? 'btn-primary' : 'btn-outline-primary' }}">
                                <i class="fas fa-video"></i> Vidéos
                            </a>
                            <a href="{{ route('media.create', 'pdfs') }}"
                                class="btn {{ $type === 'pdfs' ? 'btn-primary' : 'btn-outline-primary' }}">
                                <i class="fas fa-file-pdf"></i> PDFs
                            </a>
                            <a href="{{ route('media.create', 'txt_files') }}"
                                class="btn {{ $type === 'txt_files' ? 'btn-primary' : 'btn-outline-primary' }}">
                                <i class="fas fa-file-alt"></i> Textes
                            </a>
                        </div>
                    </div>

                    <form action="{{ route('media.store', $type) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="file" class="form-label">
                                <i class="fas fa-upload"></i>
                                Sélectionner un fichier
                                <small class="text-muted">
                                    @if ($type === 'images')
                                        (Formats: jpg, jpeg, png, gif, webp - Max: 10MB)
                                    @elseif($type === 'videos')
                                        (Formats: mp4, webm - Max: 2GB)
                                    @elseif($type === 'pdfs')
                                        (Format: pdf - Max: 10MB)
                                    @else
                                        (Formats: txt, md - Max: 10MB)
                                    @endif
                                </small>
                            </label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror" id="file"
                                name="file" required
                                accept="{{ $type === 'images' ? 'image/*' : ($type === 'videos' ? 'video/mp4,video/webm' : ($type === 'pdfs' ? '.pdf' : '.txt,.md')) }}">
                            @error('file')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="name" class="form-label fw-bold">
                                <i class="bi bi-tag"></i>
                                Nom du fichier
                            </label>
                            <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror"
                                id="name" name="name" required placeholder="Entrez un nom pour ce fichier"
                                value="{{ old('name') }}">
                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-bold">
                                <i class="bi bi-text-paragraph"></i>
                                Description (optionnel)
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                rows="3" placeholder="Ajoutez une description pour ce fichier">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input @error('is_public') is-invalid @enderror" type="checkbox"
                                    id="is_public" name="is_public" value="1">
                                <label class="form-check-label fw-bold" for="is_public">
                                    <i class="bi bi-globe"></i>
                                    Rendre ce fichier public
                                </label>
                                <div class="form-text">
                                    Les fichiers publics peuvent être vus par tous les utilisateurs connectés.
                                </div>
                                @error('is_public')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Aperçu pour les images -->
                        @if ($type === 'images')
                            <div class="mb-3" id="imagePreview" style="display: none;">
                                <label class="form-label">Aperçu de l'image:</label>
                                <div class="border rounded p-3 text-center">
                                    <img id="previewImg" src="#" alt="Aperçu" class="img-fluid"
                                        style="max-height: 200px;">
                                    <div class="mt-2">
                                        <small class="text-muted" id="imageInfo"></small>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Informations pour les vidéos -->
                        @if ($type === 'videos')
                            <div class="mb-3" id="videoInfo" style="display: none;">
                                <label class="form-label">Informations de la vidéo:</label>
                                <div class="border rounded p-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-video fa-2x text-primary me-3"></i>
                                        <div>
                                            <div class="fw-bold" id="videoName"></div>
                                            <small class="text-muted" id="videoSize"></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Indicateur de progression -->
                        <div id="uploadProgress" style="display: none;" class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                                    <span class="visually-hidden">Chargement...</span>
                                </div>
                                <span class="fw-bold">Téléversement en cours...</span>
                                <span id="progressPercent" class="ms-auto text-primary">0%</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div id="progressBar"
                                    class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                    role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0"
                                    aria-valuemax="100">
                                </div>
                            </div>
                            <div class="mt-2">
                                <small class="text-muted">
                                    <span id="uploadStatus">Préparation du téléversement...</span>
                                </small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('media.index', $type) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                            <button type="submit" id="uploadBtn" class="btn btn-primary">
                                <i class="fas fa-upload"></i>
                                Uploader le fichier
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
                        <i class="fas fa-info-circle"></i>
                        Informations
                    </h6>
                </div>
                <div class="card-body">
                    @if ($type === 'images')
                        <h6>Formats acceptés:</h6>
                        <ul class="small">
                            <li>JPG/JPEG - Pour les photos</li>
                            <li>PNG - Pour les images avec transparence</li>
                            <li>GIF - Pour les animations</li>
                            <li>WebP - Format moderne, compression optimale</li>
                        </ul>
                    @elseif($type === 'videos')
                        <h6>Formats acceptés:</h6>
                        <ul class="small">
                            <li>MP4 (H.264) - Compatible avec tous les navigateurs</li>
                            <li>WebM (VP9) - Format open-source, compression efficace</li>
                        </ul>
                        <div class="alert alert-info small">
                            <i class="fas fa-lightbulb"></i>
                            <strong>Conseil:</strong> Pour une meilleure compatibilité, utilisez le format MP4.
                        </div>
                    @elseif($type === 'pdfs')
                        <h6>Format accepté:</h6>
                        <ul class="small">
                            <li>PDF - Documents PDF standards</li>
                        </ul>
                    @else
                        <h6>Formats acceptés:</h6>
                        <ul class="small">
                            <li>TXT - Fichiers texte brut</li>
                            <li>MD - Fichiers Markdown</li>
                        </ul>
                    @endif

                    <hr>

                    <h6>Limites de taille:</h6>
                    <ul class="small">
                        <li>
                            @if ($type === 'videos')
                                <strong>2Go</strong> pour les vidéos
                            @else
                                <strong>10MB</strong> les images et les documents
                            @endif
                        </li>
                    </ul>
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
                const preview = document.getElementById('imagePreview');
                const previewImg = document.getElementById('previewImg');
                const imageInfo = document.getElementById('imageInfo');

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
                const videoInfo = document.getElementById('videoInfo');
                const videoName = document.getElementById('videoName');
                const videoSize = document.getElementById('videoSize');

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

    <!-- Script pour l'indicateur de progression -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form[method="POST"]');
            const uploadBtn = document.getElementById('uploadBtn');
            const uploadProgress = document.getElementById('uploadProgress');
            const progressBar = document.getElementById('progressBar');
            const progressPercent = document.getElementById('progressPercent');
            const uploadStatus = document.getElementById('uploadStatus');

            if (form && uploadBtn) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    // Désactiver le bouton et montrer la progression
                    uploadBtn.disabled = true;
                    uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Téléversement...';
                    uploadProgress.style.display = 'block';

                    // Créer FormData pour l'upload
                    const formData = new FormData(form);

                    // Créer XMLHttpRequest pour suivre la progression
                    const xhr = new XMLHttpRequest();

                    // Suivre la progression
                    xhr.upload.addEventListener('progress', function(e) {
                        if (e.lengthComputable) {
                            const percentComplete = Math.round((e.loaded / e.total) * 100);
                            progressBar.style.width = percentComplete + '%';
                            progressBar.setAttribute('aria-valuenow', percentComplete);
                            progressPercent.textContent = percentComplete + '%';

                            // Mettre à jour le statut
                            if (percentComplete < 25) {
                                uploadStatus.textContent = 'Démarrage du téléversement...';
                            } else if (percentComplete < 50) {
                                uploadStatus.textContent = 'Téléversement en cours...';
                            } else if (percentComplete < 75) {
                                uploadStatus.textContent = 'Téléversement presque terminé...';
                            } else if (percentComplete < 100) {
                                uploadStatus.textContent = 'Finalisation du téléversement...';
                            } else {
                                uploadStatus.textContent =
                                    'Traitement du fichier sur le serveur...';
                            }
                        }
                    });

                    // Gérer la fin du téléversement
                    xhr.addEventListener('load', function() {
                        if (xhr.status === 200) {
                            uploadStatus.textContent = 'Téléversement réussi! Redirection...';
                            progressBar.classList.remove('progress-bar-animated');
                            progressBar.classList.add('bg-success');

                            // Rediriger vers la page de succès
                            setTimeout(() => {
                                window.location.href = xhr.responseURL ||
                                    '{{ route('media.index', $type) }}';
                            }, 1000);
                        } else {
                            // Erreur
                            uploadStatus.textContent = 'Erreur lors du téléversement';
                            progressBar.classList.remove('progress-bar-animated');
                            progressBar.classList.add('bg-danger');
                            uploadBtn.disabled = false;
                            uploadBtn.innerHTML =
                                '<i class="fas fa-upload"></i> Uploader le fichier';

                            // Afficher le message d'erreur
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'alert alert-danger mt-3';
                            errorDiv.innerHTML =
                                '<i class="fas fa-exclamation-triangle"></i> Erreur lors du téléversement. Veuillez réessayer.';
                            form.appendChild(errorDiv);
                        }
                    });

                    // Gérer les erreurs réseau
                    xhr.addEventListener('error', function() {
                        uploadStatus.textContent = 'Erreur réseau';
                        progressBar.classList.remove('progress-bar-animated');
                        progressBar.classList.add('bg-danger');
                        uploadBtn.disabled = false;
                        uploadBtn.innerHTML = '<i class="fas fa-upload"></i> Uploader le fichier';
                    });

                    // Envoyer la requête
                    xhr.open('POST', form.action);
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

                    // Ajouter le token CSRF
                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (csrfToken) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken.getAttribute('content'));
                    }

                    xhr.send(formData);
                });
            }
        });
    </script>
@endpush
