<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Evaluation;
use App\Models\Question;
use App\Models\Answer;

class EvaluationManager extends Component
{
    public $evaluatableType;
    public $evaluatableId;
    public $evaluation;
    public $questions = [];
    public $newQuestion = ['type' => 'single_choice', 'text' => ''];
    public $newAnswers = [];

    public function mount($evaluatableType, $evaluatableId)
    {
        $this->evaluatableType = $evaluatableType;
        $this->evaluatableId = $evaluatableId;

        $this->evaluation = Evaluation::firstOrCreate(
            ['evaluatable_type' => $evaluatableType, 'evaluatable_id' => $evaluatableId],
            ['title' => 'Nouvelle évaluation', 'description' => '']
        );

        $this->questions = $this->evaluation->questions()->with('answers')->get()->toArray();
    }

    public function addQuestion()
    {
        $question = $this->evaluation->questions()->create([
            'type' => $this->newQuestion['type'],
            'question_text' => $this->newQuestion['text']
        ]);

        foreach ($this->newAnswers as $answer) {
            $question->answers()->create([
                'answer_text' => $answer['text'],
                'is_correct' => $answer['is_correct'] ?? false,
            ]);
        }

        $this->mount($this->evaluatableType, $this->evaluatableId); // Rafraîchir les données
    }

    public function render()
    {
        return view('livewire.evaluation-manager', [
            'questions' => $this->questions,
        ]);
    }
}
