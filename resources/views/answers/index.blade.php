@extends('layouts.authenticated.owners.index')

@section('dashboard-content')
    {{-- @livewire('chapter-manager') --}}

    <!-- Liste des chapitres -->
    <div class="mt-3">
        <!-- Bouton Ajouter un chapitre -->
        <a href="{{ route('chapters.create') }}" class="btn btn-primary mb-3">
            <i class="bi bi-plus-circle"></i> Ajouter un chapitre
        </a>
        <h3>Liste des chapitres</h3>
        <table class="table table-striped " id="dataTable">
            <thead>
                <tr>
                    <th>Cours</th>
                    <th>Titre</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($chapters as $chapter)
                    <tr>
                        <td>{{ $chapter->course->title ?? 'Cours non défini' }}</td>
                        <td>{{ $chapter->title }}</td>
                        <td>
                            <a href="{{ route('chapters.show', $chapter->id) }}" class="btn btn-info btn-sm">
                                Lire
                            </a>

                            <a href="{{ route('chapters.edit', $chapter->id) }}" class="btn btn-warning btn-sm">
                                Modifier
                            </a>


                            <button wire:click="deleteChapter({{ $chapter->id }})" class="btn btn-danger btn-sm">
                                Supprimer
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
