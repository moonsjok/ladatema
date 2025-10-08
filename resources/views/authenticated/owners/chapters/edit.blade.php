@extends('layouts.authenticated.owners.index')
@section('page-title', 'Modifier le chapitre : ' . $chapter->title)

@section('dashboard-content')
    <div class="container-fluid  no-plyr">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    {{-- <div class="card-header">Modifier le chapitre</div> --}}
                    <div class="card-body">
                        <form action="{{ route('chapters.update', $chapter->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="course_id" class="form-label">Le cours</label>
                                <select class="form-select @error('course_id') is-invalid @enderror" id="course_id"
                                    name="course_id" required>
                                    <option value="">Sélectionnez changer de cours</option>
                                    @foreach ($courses as $course)
                                        <option value="{{ $course->id }}"
                                            {{ old('course_id', $chapter->course_id) == $course->id ? 'selected' : '' }}>
                                            {{ $course->title }}</option>
                                    @endforeach
                                </select>
                                @error('course_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="numero" class="form-label">Numero du chapitre</label>
                                <input type="text" class="form-control @error('numero') is-invalid @enderror"
                                    id="numero" name="numero" value="{{ old('numero', $chapter->numero) }}" required>
                                @error('numero')
                                    <div class="invalid-feedback">{{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="title" class="form-label">Le titre du chapitre</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror"
                                    id="title" name="title" value="{{ old('title', $chapter->title) }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                {{-- <label for="content" class="form-label">Contenu</label> --}}
                                <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="5"
                                    required>{{ old('content', $chapter->content) }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">Mettre à jour</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        video {
            width: 100% !important;
        }
    </style>
@endsection
