@extends('layouts.guest.index')

@section('page-title', 'Liste des formations') <!-- Titre de la page -->

@section('content')

    <div class="container my-5">
        <h1 class="text-center text-primary mb-4">Liste des formations</h1>

        <!-- Liste des formations -->
        <div class="list-group">
            @foreach ($formations as $formation)
                <a href="{{ route('guest.formations.show', $formation->id) }}" class="list-group-item list-group-item-action">
                    <div class="row align-items-center">
                        <!-- Colonne pour l'icône -->
                        <div class="col-2 col-md-1 text-center">
                            <i class="bi bi-mortarboard-fill text-primary fs-4"></i>
                        </div>

                        <!-- Colonne pour les détails de la formation -->
                        <div class="col-10 col-md-11">
                            <h5 class="mb-1">{{ $formation->title }}</h5>
                            <small class="text-muted mb-1">{{ $formation->courses->count() }} cours</small>
                            <p><strong> Cout : {{ $formation->price }} </strong> </p>
                            <p class="mb-0 text-muted text-justify">{{ $formation->description }}</p>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="d-grid gap-2 d-md-block">
                                <a href="{{ route('subscriptions.createAccount', ['type' => 'formation', 'id' => $formation->id]) }}"
                                    class="btn btn-primary">Suivre la formation</a>
                                <a href="{{ route('guest.formations.show', $formation->id) }}" class="btn btn-primary">en
                                    savoir plus</a>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $formations->links() }}
        </div>
    </div>
@endsection
