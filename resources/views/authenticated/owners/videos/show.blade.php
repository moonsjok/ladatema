@extends('layouts.authenticated.owners.index')

@section('page-title', 'Visualiser la Vidéo')

@section('dashboard-content')

    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">Visualiser la Vidéo <i class="bi bi-play-btn"></i></h1>
                <div class="card mb-3">
                    <div class="card-body">
                        <video class="w-100" controls>
                            <source src="{{ $videoUrl }}" type="video/mp4">
                            Votre navigateur ne supporte pas la balise vidéo.
                        </video>
                        <h5 class="card-title mt-3">{{ $videoName }}</h5>
                        <div class="mt-3">
                            <a href="{{ route('videos.list') }}" class="btn btn-secondary me-2">
                                <i class="bi bi-arrow-left"></i> Retour à la liste
                            </a>
                            <form action="{{ route('video.delete', $videoName) }}" method="GET" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-danger"
                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette vidéo ?')">
                                    <i class="bi bi-trash"></i> Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
