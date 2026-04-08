@extends('layouts.authenticated.owners.index')

@section('page-title', 'Détails du média')

@section('dashboard-content')
<div class="col-md-10">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-file-earmark"></i>
                Détails du média
            </h5>
            <div class="d-flex gap-2">
                <a href="{{ route('media.index', $media->collection_name) }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Retour
                </a>
                @if(auth()->user()->role === 'dev' || auth()->user()->role === 'owner' || 
                  ($media->model_type === \App\Models\User::class && $media->model_id === auth()->id()))
                    <a href="{{ route('media.edit', [$media->collection_name, $media->id]) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Modifier
                    </a>
                @endif
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <!-- Aperçu du fichier -->
                    <div class="mb-4">
                        <h6>Aperçu</h6>
                        <div class="border rounded p-3 text-center bg-light">
                            @if($media->collection_name === 'images')
                                <img src="{{ $media->getUrl() }}" alt="{{ $media->name }}" class="img-fluid" style="max-height: 400px;">
                            @elseif($media->collection_name === 'videos')
                                <video controls class="img-fluid" style="max-height: 400px;">
                                    <source src="{{ route('media.file.secure', $media->id) }}" type="video/mp4">
                                    Votre navigateur ne supporte pas la lecture vidéo.
                                </video>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-file-earmark-text fs-1 text-muted"></i>
                                    <p class="mt-3 mb-0">
                                        <a href="{{ route('media.file.secure', $media->id) }}" class="btn btn-primary" target="_blank">
                                            <i class="bi bi-download"></i> Télécharger le fichier
                                        </a>
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mb-4">
                        <h6>Actions</h6>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('media.file.secure', $media->id) }}" 
                               class="btn btn-primary" 
                               target="_blank">
                                <i class="bi bi-eye"></i> Voir/Télécharger
                            </a>
                            
                            <button class="btn btn-info" 
                                    onclick="copyUrl('{{ route('media.file.secure', $media->id) }}', '{{ $media->getCustomProperty('name', $media->name) }}')">
                                <i class="bi bi-clipboard"></i> Copier l'URL
                            </button>
                            
                            @if(auth()->user()->role === 'dev' || auth()->user()->role === 'owner' || 
                              ($media->model_type === \App\Models\User::class && $media->model_id === auth()->id()))
                                <a href="{{ route('media.edit', [$media->collection_name, $media->id]) }}" 
                                   class="btn btn-warning">
                                    <i class="bi bi-pencil"></i> Modifier
                                </a>
                                
                                <form action="{{ route('media.destroy', [$media->collection_name, $media->id]) }}" 
                                      method="POST" 
                                      style="display: inline-block;"
                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce média ?')">
                                    {{ csrf_field() }} {{ method_field('DELETE') }}
                                    <button type="submit" class="btn btn-danger">
                                        <i class="bi bi-trash"></i> Supprimer
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- Informations -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="bi bi-info-circle"></i> Informations
                            </h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Nom:</strong></td>
                                    <td>{{ $media->getCustomProperty('name', $media->name) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Type:</strong></td>
                                    <td>
                                        @if($media->collection_name === 'images')
                                            <span class="badge bg-primary">Image</span>
                                        @elseif($media->collection_name === 'videos')
                                            <span class="badge bg-success">Vidéo</span>
                                        @else
                                            <span class="badge bg-info">Document</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Taille:</strong></td>
                                    <td>{{ number_format($media->size / 1024, 2) }} KB</td>
                                </tr>
                                <tr>
                                    <td><strong>Visibilité:</strong></td>
                                    <td>
                                        @if($media->getCustomProperty('is_public', false))
                                            <span class="badge bg-success">Public</span>
                                        @else
                                            <span class="badge bg-warning">Privé</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Propriétaire:</strong></td>
                                    <td>
                                        @if($media->model_type === \App\Models\User::class)
                                            @php
                                                $user = \App\Models\User::find($media->model_id);
                                            @endphp
                                            {{ $user ? $user->name : 'Utilisateur supprimé' }}
                                        @else
                                            Système
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Uploadé le:</strong></td>
                                    <td>{{ $media->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                @if($media->getCustomProperty('description'))
                                    <tr>
                                        <td><strong>Description:</strong></td>
                                        <td>{{ $media->getCustomProperty('description') }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

@push('scripts')
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
@endsection
