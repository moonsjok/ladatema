@extends('layouts.authenticated.owners.index')

@section('page-title', 'Tableau de bord')

@section('dashboard-content')

    <div class="container">
        <h1 class="mb-4">Liste des Vidéos <i class="bi bi-film"></i></h1>
        <div class="mb-3">
            <a href="{{ route('video.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Ajouter une vidéo
            </a>
        </div>
        @if (isset($message))
            <div class="alert alert-warning" role="alert">
                {{ $message }}
            </div>
        @else
            <div class="list-group">
                @forelse ($videos as $video)
                    <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <a href="{{ route('video.show', $video->getFilename()) }}" class="text-decoration-none text-reset">
                            {{ $video->getFilename() }}
                        </a>
                        <div class="d-flex">
                            <a href="{{ route('video.show', $video->getFilename()) }}" class="btn btn-sm btn-info me-2">
                                <i class="bi bi-eye"></i> Voir
                            </a>
                            <form action="{{ route('video.delete', $video->getFilename()) }}" method="GET"
                                class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette vidéo ?')">
                                    <i class="bi bi-trash"></i> Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="alert alert-warning" role="alert">
                        Aucune vidéo n'a été trouvée dans le dossier.
                    </div>
                @endforelse
            </div>
        @endif
    </div>

@endsection
