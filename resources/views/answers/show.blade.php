@extends('layouts.authenticated.owners.index')
@section('page-title', 'Chapitre ' . $chapter->numero . ' : ' . $chapter->title)
@section('dashboard-content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <table class="table bg-transparent">
                            <tr>
                                <td><strong>Cours</strong></td>
                                <td><strong>:</strong></td>
                                <td>{{ $chapter->course->title ?? 'Non spécifié' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Chapitre {{ $chapter->numero }}</strong></td>
                                <td><strong>:</strong></td>
                                <td>{{ $chapter->title }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="card-body">
                        <p class="card-text"></p>
                        <p class="card-text"> {!! $chapter->content !!}</p>
                        <a href="{{ route('chapters.edit', $chapter->id) }}" class="btn btn-primary">Éditer</a>
                        <form action="{{ route('chapters.destroy', $chapter->id) }}" method="POST" style="display:inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger"
                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce chapitre ?')">Supprimer</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
