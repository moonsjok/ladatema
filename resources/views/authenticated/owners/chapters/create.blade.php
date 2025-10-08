@extends('layouts.authenticated.owners.index')

@section('page-title', 'Ajouter un chapitre')

@section('dashboard-content')
    <div class="container-fluid  no-plyr ">
        <h1>Créer un chapitre</h1>

        @if (!$selectedCourseId)
            <div class="alert alert-warning">
                <strong>Attention :</strong> Vous devez d'abord sélectionner un cours avant d'ajouter un chapitre.
            </div>
            <a href="{{ route('chapters.index') }}" class="btn btn-primary">Retour à la sélection</a>
        @else
            <form action="{{ route('chapters.store') }}" method="POST">
                @csrf

                <!-- Sélection du cours (désactivé si un cours est déjà choisi) -->
                <div class="mb-3">
                    <label for="course_id" class="form-label">Le cours</label>
                    <select class="form-select @error('course_id') is-invalid @enderror" id="course_id" name="course_id"
                        required {{ $selectedCourseId ? 'disabled' : '' }}>
                        <option value="">-- Sélectionnez un cours --</option>
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}" {{ $course->id == $selectedCourseId ? 'selected' : '' }}>
                                {{ $course->title }}
                            </option>
                        @endforeach
                    </select>
                    @if ($selectedCourseId)
                        <input type="hidden" name="course_id" value="{{ $selectedCourseId }}">
                    @endif
                    @error('course_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Numéro du chapitre -->
                <div class="mb-3">
                    <label for="numero" class="form-label">Numéro du chapitre</label>
                    <input type="text" class="form-control @error('numero') is-invalid @enderror" id="numero"
                        name="numero" value="{{ old('numero') }}" required>
                    @error('numero')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Titre du chapitre -->
                <div class="mb-3">
                    <label for="title" class="form-label">Le titre du chapitre</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                        name="title" value="{{ old('title') }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Contenu du chapitre -->
                <div class="mb-3">
                    <label for="content" class="form-label">Contenu</label>
                    <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="5"
                        required>{{ old('content') }}</textarea>
                    @error('content')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Ajouter</button>
            </form>
        @endif
    </div>

    <style>
        video {
            width: 100% !important;
        }
    </style>
@endsection
