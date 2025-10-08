<?php

namespace App\Livewire;

use App\Models\Question;
use App\Models\Answer;
use Livewire\Component;

class QuestionForm extends Component
{
    public $evaluationId;
    public $questionText;
    public $showForm;
    public $type = 'text'; // Par défaut, le type de question est "texte"
    public $answers = []; // Tableau pour stocker les réponses dynamiques

    protected $rules = [
        'questionText' => 'required|string|max:255',
        'type' => 'required|string',
        'answers.*.answer_text' => 'required|string|max:255',
        'answers.*.is_correct' => 'nullable|boolean',
    ];

    public function mount($evaluationId)
    {
        $this->evaluationId = $evaluationId;
        $this->answers = [
            ['answer_text' => '', 'is_correct' => false],
        ];
    }
    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
    }
    public function addAnswer()
    {
        $this->answers[] = ['answer_text' => '', 'is_correct' => false];
    }

    public function removeAnswer($index)
    {
        unset($this->answers[$index]);
        $this->answers = array_values($this->answers);
    }

    public function save()
    {
        $this->validate();

        $question = Question::create([
            'evaluation_id' => $this->evaluationId,
            'type' => $this->type,
            'question_text' => $this->questionText,
        ]);

        foreach ($this->answers as $answer) {
            $question->answers()->create($answer);
        }

        session()->flash('success', 'La question et les réponses ont été ajoutées avec succès.');
        return redirect()->route('evaluations.show', $this->evaluationId);
    }

    public function render()
    {
        return view('livewire.question-form');
    }
}
