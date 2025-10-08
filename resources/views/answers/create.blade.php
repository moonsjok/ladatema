@extends('layouts.authenticated.owners.index')
@section('page-title', 'Ajouter une réponse')

@section('dashboard-content')
    <div class="container">
        <h1>Ajouter une réponse</h1>
        <form action="{{ route('answers.store') }}" method="POST">
            @csrf
            <input type="hidden" name="question_id" value="{{ $question->id }}">
            <div class="mb-3">
                <label for="content" class="form-label">Contenu de la réponse</label>
                <textarea class="form-control" id="content" name="content" rows="3" required></textarea>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="is_correct" name="is_correct">
                <label class="form-check-label" for="is_correct">Bonne réponse</label>
            </div>
            <button type="submit" class="btn btn-primary">Ajouter</button>
        </form>
    </div>

@endsection
@push('scripts')
@endpush
