@extends('layouts.authenticated.owners.index')

@section('page-title', 'Ajouter une Vidéo')

@section('dashboard-content')

    <div class="container">
        <h1 class="mb-4">Ajouter une Vidéo <i class="bi bi-plus-circle"></i></h1>
        <form action="{{ route('video.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="video" class="form-label">Choisir une vidéo</label>
                <input class="form-control" type="file" id="video" name="video" accept="video/mp4,video/quicktime"
                    required>
                @error('video')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">Uploader</button>
        </form>
    </div>
@endsection
