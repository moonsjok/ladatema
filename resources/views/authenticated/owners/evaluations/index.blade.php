<!-- resources/views/evaluations/index.blade.php -->
@extends('layouts.authenticated.owners.index')
@section('page-title', 'Évaluations ')
@section('dashboard-content')
    <div class="container">

        <!-- Bouton Ajouter une évaluation -->
        <a href="{{ route('evaluations.create.step1') }}" class="btn btn-primary mb-3">
            <i class="bi bi-plus-circle"></i> Ajouter une évaluation
        </a>
        <h1 class="mb-4">Liste des évaluations</h1>

        <!-- Onglets pour séparer les évaluations -->
        <ul class="nav nav-tabs" id="evaluationTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="formations-tab" data-bs-toggle="tab" data-bs-target="#formations"
                    type="button" role="tab" aria-controls="formations" aria-selected="true">
                    Formations
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="courses-tab" data-bs-toggle="tab" data-bs-target="#courses" type="button"
                    role="tab" aria-controls="courses" aria-selected="false">
                    Cours
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="chapters-tab" data-bs-toggle="tab" data-bs-target="#chapters" type="button"
                    role="tab" aria-controls="chapters" aria-selected="false">
                    Chapitres
                </button>
            </li>
        </ul>

        <!-- Contenu des onglets -->
        <div class="tab-content mt-4" id="evaluationTabsContent">
            <!-- Évaluations de formations -->
            <div class="tab-pane fade show active" id="formations" role="tabpanel" aria-labelledby="formations-tab">
                @if ($formationEvaluations->isEmpty())
                    <p>Aucune évaluation pour les formations.</p>
                @else
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nom de la formation</th>
                                <th>Titre de l'évaluation</th>
                                <th>Importance</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($formationEvaluations as $key => $evaluation)
                                <tr>
                                    <td> {{ $key + 1 }} {{-- $evaluation->id --}}</td>
                                    <td>{{ $evaluation->evaluatable->title }}</td>
                                    <td>{{ $evaluation->title }}</td>
                                    <td>
                                        <span class="badge bg-{{ $evaluation->importance_color }}">
                                            {{ $evaluation->importance_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('evaluations.show', $evaluation->id) }}"
                                            class="btn btn-info btn-sm" title="Voir">
                                            <i class="bi bi-eye"></i> 
                                        </a>
                                        <a href="{{ route('evaluations.edit.step1', $evaluation->id) }}"
                                            class="btn btn-warning btn-sm"  title="Modifier">
                                            <i class="bi bi-pencil"></i> 
                                        </a>
                                        <form action="{{ route('evaluations.destroy', $evaluation->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette évaluation ?');" title="Supprimer" >
                                                <i class="bi bi-trash"></i> 
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            <!-- Évaluations de cours -->
            <div class="tab-pane fade" id="courses" role="tabpanel" aria-labelledby="courses-tab">
                @if ($courseEvaluations->isEmpty())
                    <p>Aucune évaluation pour les cours.</p>
                @else
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nom du cours</th>
                                <th>Titre de l'évaluation</th>
                                <th>Importance</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($courseEvaluations as $key => $evaluation)
                                <tr>
                                    <td> {{ $key + 1 }} {{-- $evaluation->id --}}</td>
                                    <td>{{ $evaluation->evaluatable->title }}</td>
                                    <td>{{ $evaluation->title }}</td>
                                    <td>
                                        <span class="badge bg-{{ $evaluation->importance_color }}">
                                            {{ $evaluation->importance_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('evaluations.show', $evaluation->id) }}"
                                            class="btn btn-info btn-sm" title="Voir">
                                            <i class="bi bi-eye"></i> 
                                        </a>
                                        <a href="{{ route('evaluations.edit.step1', $evaluation->id) }}"
                                            class="btn btn-warning btn-sm" title="Modifier">
                                            <i class="bi bi-pencil"></i> 
                                        </a>
                                        <form action="{{ route('evaluations.destroy', $evaluation->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette évaluation ?');" title="Supprimer">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            <!-- Évaluations de chapitres -->
            <div class="tab-pane fade" id="chapters" role="tabpanel" aria-labelledby="chapters-tab">
                @if ($chapterEvaluations->isEmpty())
                    <p>Aucune évaluation pour les chapitres.</p>
                @else
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nom du chapitre</th>
                                <th>Titre de l'évaluation</th>
                                <th>Importance</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($chapterEvaluations as $key => $evaluation)
                                <tr>
                                    <td> {{ $key + 1 }} {{-- $evaluation->id --}}</td>
                                    <td>{{ $evaluation->evaluatable->title }}</td>
                                    <td>{{ $evaluation->title }}</td>
                                    <td>
                                        <span class="badge bg-{{ $evaluation->importance_color }}">
                                            {{ $evaluation->importance_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('evaluations.show', $evaluation->id) }}"
                                            class="btn btn-info btn-sm" title="Voir">
                                            <i class="bi bi-eye"></i> 
                                        </a>
                                        <a href="{{ route('evaluations.edit.step1', $evaluation->id) }}"
                                            class="btn btn-warning btn-sm" title="Modifier">
                                            <i class="bi bi-pencil"></i> 
                                        </a>
                                        <form action="{{ route('evaluations.destroy', $evaluation->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette évaluation ?');" title="Supprimer">
                                                <i class="bi bi-trash"></i> Supprimer
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
@endsection
