{{-- resources/views/evaluations/edit.blade.php --}}
@extends('layouts.authenticated.owners.index')
@section('page-title', 'Modifier une évaluation')

@section('dashboard-content')

    <div class="container">
        <h1>Modifier une évaluation</h1>
        <form action="{{ route('evaluations.update', $evaluation->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="title" class="form-label">Titre</label>
                <input type="text" class="form-control" id="title" name="title"
                    value="{{ old('title', $evaluation->title) }}" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" required>{{ old('description', $evaluation->description) }}</textarea>
            </div>
            <div class="mb-3">
                <label for="model_type" class="form-label">Associer à</label>
                <select class="form-select" id="model_type" name="model_type" required>
                    <option value="App\Models\Formation" @if ($evaluation->evaluatable_type === 'App\\Models\\Formation') selected @endif>Formation
                    </option>
                    <option value="App\Models\Course" @if ($evaluation->evaluatable_type === 'App\\Models\\Course') selected @endif>Cours</option>
                    <option value="App\Models\Chapter" @if ($evaluation->evaluatable_type === 'App\\Models\\Chapter') selected @endif>Chapitre</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="model_id" class="form-label">Sélectionnez l'élément</label>
                <select class="form-select" id="model_id" name="model_id" required>
                    @foreach ($evaluatables as $evaluatable)
                        <option value="{{ $evaluatable->id }}" @if ($evaluation->evaluatable_id == $evaluatable->id) selected @endif>
                            {{ $evaluatable->title }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Mettre à jour</button>
            <a href="{{ route('evaluations.show', $evaluation->id) }}" class="btn btn-secondary">Annuler</a>
        </form>
    </div>

    <script>
        document.getElementById('model_type').addEventListener('change', function() {
            const modelType = this.value;
            const modelIdSelect = document.getElementById('model_id');

            modelIdSelect.innerHTML = '<option value="">Chargement...</option>';

            fetch(`/get-models?type=${modelType}`)
                .then(response => response.json())
                .then(data => {
                    modelIdSelect.innerHTML = '<option value="">-- Sélectionnez --</option>';
                    data.forEach(item => {
                        modelIdSelect.innerHTML +=
                            `<option value="${item.id}">${item.name || item.title}</option>`;
                    });
                });
        });
    </script>
@endsection
