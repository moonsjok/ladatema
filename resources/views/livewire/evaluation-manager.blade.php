<div>
    <h3>Gestion des évaluations</h3>
    <div>
        <h5>Questions</h5>
        <ul>
            @foreach ($questions as $question)
                <li>
                    {{ $question['question_text'] }} ({{ ucfirst($question['type']) }})
                    <ul>
                        @foreach ($question['answers'] as $answer)
                            <li>
                                {{ $answer['answer_text'] }} 
                                @if ($answer['is_correct']) <strong>(Correcte)</strong> @endif
                            </li>
                        @endforeach
                    </ul>
                </li>
            @endforeach
        </ul>
    </div>

    <button class="btn btn-primary" wire:click="addQuestion">Ajouter une question</button>
</div>
