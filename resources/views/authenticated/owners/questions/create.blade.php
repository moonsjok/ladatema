@extends('layouts.authenticated.owners.index')
@section('page-title', "Ajouter une question à l'évaluation")

@section('dashboard-content')

    <div class="container">
        <h1>Ajouter une question pour : {{ $evaluation->title }}</h1>

        <form action="{{ route('questions.store', $evaluation) }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="question_text" class="form-label">Texte de la question</label>
                <input type="text" class="form-control" id="question_text" name="question_text" required>
            </div>
            <div class="mb-3">
                <label for="type" class="form-label">Type de question</label>
                <select class="form-select" id="type" name="type" required>
                    <option value="single_choice">Choix unique</option>
                    <option value="multiple_choice">Choix multiple</option>
                </select>
            </div>
            <div id="answers-container">
                <h5>Réponses</h5>
                <div class="answer-item">
                    <input type="text" name="answers[0][text]" placeholder="Texte de la réponse" required>
                    <input type="checkbox" name="answers[0][is_correct]"> Correcte
                </div>
            </div>
            <button type="button" id="add-answer" class="btn btn-secondary">Ajouter une réponse</button>
            <button type="submit" class="btn btn-primary">Enregistrer la question</button>
        </form>
    </div>

    <script>
        let answerIndex = 1;
        document.getElementById('add-answer').addEventListener('click', function() {
            const container = document.getElementById('answers-container');
            const div = document.createElement('div');
            div.classList.add('answer-item');
            div.innerHTML = `
            <input type="text" name="answers[${answerIndex}][text]" placeholder="Texte de la réponse" required>
            <input type="checkbox" name="answers[${answerIndex}][is_correct]"> Correcte
        `;
            container.appendChild(div);
            answerIndex++;
        });
    </script>

@endsection
