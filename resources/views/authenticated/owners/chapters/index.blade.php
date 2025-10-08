@extends('layouts.authenticated.owners.index')

@section('dashboard-content')

    <!-- Sélection et affichage du cours dans une carte avec boutons -->
    <div class="card my-4">
        <div class="card-body">
            @if ($selectedCourseId)
                <div class=" mb-3">
                    <p class="text-center text-muted"> Cours </p>
                    <h2 class=" text-center card-title mb-0 fw-bold">
                        @foreach ($courses as $course)
                            @if ($course->id == $selectedCourseId)
                                {{ $course->title }}
                            @endif
                        @endforeach
                    </h2>
                    <br />
                    <form method="GET" action="{{ route('chapters.index') }}" class="d-inline">
                        <button type="submit" name="change_course" class="btn btn-outline-primary btn-sm">
                            Changer de cours
                        </button>
                    </form>
                </div>
            @else
                <h5 class="card-title">Choisissez un cours</h5>
                <p class="card-subtitle mb-3 text-muted">
                    Veuillez choisir le cours dont vous voulez lister , Ajouter,
                    Modifier ou Supprimer un ou des chapitre(s)
                </p>
                <div class="d-flex flex-wrap mb-3">
                    @foreach ($courses as $course)
                        <form method="GET" action="{{ route('chapters.index') }}"
                            style="margin-right: 10px; margin-bottom: 10px;">
                            <button type="submit" name="course_id" value="{{ $course->id }}"
                                class="btn btn-outline-secondary">
                                {{ $course->title }}
                            </button>
                        </form>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="card p-3">
        <div class="card-body">

            @if ($selectedCourseId)
                <!-- Bouton Ajouter un chapitre -->
                <a href="{{ route('chapters.create', ['course_id' => $selectedCourseId]) }}" class="btn btn-primary mb-3">
                    <i class="bi bi-plus-circle"></i> Ajouter un chapitre
                </a>

                <!-- Liste des chapitres -->
                <h3>Liste des chapitres</h3>

                @if ($chapters->isEmpty())
                    <p>Aucun chapitre disponible pour ce cours.</p>
                @else
                    <table class="table table-striped" id="dataTable">
                        <thead>
                            <tr>
                                <th>Numéro du chapitre</th>
                                <th>Titre du chapitre</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($chapters as $chapter)
                                <tr>
                                    <td>{{ $chapter->numero ?? 'Non défini' }}</td>
                                    <td>{{ $chapter->title }}</td>
                                    <td>
                                        <a href="{{ route('chapters.show', $chapter->id) }}" class="btn btn-info btn-sm">
                                            Lire
                                        </a>

                                        <a href="{{ route('chapters.edit', [$chapter->id, 'course_id' => request()->course_id]) }}"
                                            class="btn btn-warning btn-sm">
                                            Modifier
                                        </a>

                                        <form action="{{ route('chapters.destroy', $chapter->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce chapitre ?')">
                                                Supprimer
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            @endif
        </div>
    </div>
@endsection
