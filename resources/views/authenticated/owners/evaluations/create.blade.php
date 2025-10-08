{{-- ressouces/views/evaluations/create.php --}}
@extends('layouts.authenticated.owners.index')
@section('page-title', 'Ajouter une evaluation')

@section('dashboard-content')

    <div class="container">
        <h1>Créer une évaluation</h1>
        <form action="{{ route('evaluations.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="title" class="form-label">Titre</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>
            <div class="mb-3">
                <label for="model_type" class="form-label">Associer à</label>
                <select class="form-select" id="model_type" name="model_type" required>
                    <option value="">-- Sélectionnez --</option>
                    <option value="App\Models\Formation">Formation</option>
                    <option value="App\Models\Course">Cours</option>
                    <option value="App\Models\Chapter">Chapitre</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="model_id" class="form-label">Sélectionnez l'élément</label>
                <select class="form-select" id="model_id" name="model_id" required>
                    <!-- Ce champ sera rempli dynamiquement via JavaScript ou Livewire -->
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Créer</button>
        </form>
    </div>

    <script>
        // Exemple de script pour charger dynamiquement les éléments en fonction du type sélectionné
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
@push('scripts')
@endpush
