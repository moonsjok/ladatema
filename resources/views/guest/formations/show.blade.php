@extends('layouts.guest.index')

@section('page-title', $formation->title)

@section('content')

    <div class="container my-5">
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary ">
            <i class="bi bi-arrow-left me-2"></i> Retour
        </a>
        <br />
        <h1 class="text-center text-primary mt-3 mb-4">{{ $formation->title }}</h1>

        <div class="row">
            <div class="col-md-8 offset-md-2">
                <p class="text-justify">{!! $formation->description !!}</p>
            </div>
        </div>

        <div class="text-center my-4">
            <a href="{{ route('subscriptions.createAccount', ['type' => 'formation', 'typeid' => $formation->id]) }}"
                class="btn btn-primary btn-lg">
                <i class="bi bi-play-circle-fill"></i> Démarrer la Formation
            </a>
        </div>

        <h2 class="mt-4">Cours Inclus</h2>
        <hr />
        <div class="list-group list-group-flush p-3">
            @foreach ($courses as $course)
                @if (!$course->deleted_at)
                    <a href="#"
                        class="list-group-item list-group-item-action text-decoration-none p-3 mb-3 shadow-sm">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ $course->title }}</h5>
                            <span class="badge bg-primary rounded-pill">{{ $course->chapters->count() }} chapitre(s)</span>
                        </div>
                        <hr>
                        <p class="text-muted mt-2">{!! $course->description !!}</p>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-clock me-2"></i>
                            <small class="text-muted">Durée: {{ $course->duration ?? 'N/A' }} </small>
                        </div>
                    </a>
                @endif
            @endforeach
        </div>
    </div>
@endsection
