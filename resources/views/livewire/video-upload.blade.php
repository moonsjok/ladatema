<div>
    <!-- Message de succès -->
    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <!-- Zone de téléchargement -->
            <div class="form-group mb-3">
                <label for="video" class="form-label">Téléchargez une vidéo (max 1 Go)</label>
                <input type="file" id="video" wire:model="video" class="form-control"
                    accept="video/mp4,video/webm,video/ogg,video/avi,video/mkv,video/flv" />

                <!-- Aperçu de la vidéo avant upload -->
                @if ($videoPreview)
                    <div class="mt-3">
                        <h5>Aperçu de la vidéo</h5>
                        <video width="320" height="240" controls>
                            <source src="{{ $videoPreview }}" type="video/mp4">
                            Votre navigateur ne prend pas en charge la vidéo.
                        </video>
                    </div>
                @endif

                @error('video')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <!-- Bouton Upload -->
            <button wire:click="upload" class="btn btn-primary" wire:loading.attr="disabled">
                Télécharger la vidéo
            </button>

            <!-- Indicateur d'activité -->
            <div wire:loading>
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
                <span>Chargement en cours...</span>
            </div>

            <!-- Afficher le lien de la vidéo une fois téléchargée -->
            @if ($videoPath)
                <div class="mt-3">
                    <h5>Vidéo téléchargée avec succès !</h5>
                    <a href="{{ asset('storage/' . $videoPath) }}" target="_blank">Voir la vidéo</a>
                </div>
            @endif
        </div>
    </div>
</div>
