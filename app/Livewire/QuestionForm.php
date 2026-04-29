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
    public $type = 'single_choice'; // Par défaut, le type de question est "Choix unique"
    public $answers = []; // Tableau pour stocker les réponses dynamiques
    public $correctAnswerIndex = null; // Pour le choix unique
    public $intruderAnswerIndex = null; // Pour trouver l'intrus
    public $points = 1; // Points par question (par défaut 1)

    protected $rules = [
        'questionText' => 'required|string|max:255',
        'type' => 'required|in:single_choice,multiple_choice,text,find_intruder',
        'points' => 'required|integer|min:1|max:100',
        'answers.*.answer_text' => 'required|string|max:255',
        'answers.*.is_correct' => 'nullable|boolean',
    ];

    public function mount($evaluationId)
    {
        $this->evaluationId = $evaluationId;
        // Initialiser les réponses par défaut pour le type "single_choice"
        $this->answers = [
            ['answer_text' => '', 'explanation' => '', 'is_correct' => false],
            ['answer_text' => '', 'explanation' => '', 'is_correct' => false],
        ];
    }

    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
    }

    public function addAnswer()
    {
        $this->answers[] = ['answer_text' => '', 'explanation' => '', 'is_correct' => false];
    }

    public function removeAnswer($index)
    {
        unset($this->answers[$index]);
        $this->answers = array_values($this->answers);
        
        // Réajuster les indices si nécessaire
        if ($this->correctAnswerIndex !== null && $this->correctAnswerIndex >= count($this->answers)) {
            $this->correctAnswerIndex = null;
        }
        if ($this->intruderAnswerIndex !== null && $this->intruderAnswerIndex >= count($this->answers)) {
            $this->intruderAnswerIndex = null;
        }
    }

    public function updatedType($value)
    {
        // Réinitialiser les indices quand le type change
        $this->correctAnswerIndex = null;
        $this->intruderAnswerIndex = null;
        
        // Réinitialiser les réponses selon le type
        if ($value === 'text') {
            $this->answers = [];
        } else {
            $this->answers = [
                ['answer_text' => '', 'explanation' => '', 'is_correct' => false],
                ['answer_text' => '', 'explanation' => '', 'is_correct' => false],
            ];
        }
    }

    public function save()
    {
        // Validation spécifique selon le type
        if ($this->type === 'text') {
            $this->validate([
                'questionText' => 'required|string|max:255',
                'type' => 'required|in:single_choice,multiple_choice,text,find_intruder',
            ]);
        } else {
            $this->validate([
                'questionText' => 'required|string|max:255',
                'type' => 'required|in:single_choice,multiple_choice,text,find_intruder',
                'answers' => 'required|array|min:2',
                'answers.*.answer_text' => 'required|string|max:255',
            ]);

            // Validation spécifique pour le type
            if ($this->type === 'single_choice' && $this->correctAnswerIndex === null) {
                $this->addError('correctAnswerIndex', 'Vous devez sélectionner une réponse correcte.');
                return;
            }

            if ($this->type === 'find_intruder' && $this->intruderAnswerIndex === null) {
                $this->addError('intruderAnswerIndex', 'Vous devez sélectionner un intrus.');
                return;
            }

            if ($this->type === 'multiple_choice') {
                $hasCorrectAnswer = false;
                foreach ($this->answers as $answer) {
                    if ($answer['is_correct'] ?? false) {
                        $hasCorrectAnswer = true;
                        break;
                    }
                }
                if (!$hasCorrectAnswer) {
                    $this->addError('multiple_choice_correct', 'Vous devez sélectionner au moins une réponse correcte.');
                    return;
                }
            }
        }

        $question = Question::create([
            'evaluation_id' => $this->evaluationId,
            'type' => $this->type,
            'question_text' => $this->questionText,
            'points' => $this->points,
        ]);

        // Créer les réponses seulement si le type le nécessite
        if ($this->type !== 'text') {
            foreach ($this->answers as $index => $answer) {
                $isCorrect = false;
                
                if ($this->type === 'single_choice') {
                    $isCorrect = ($index === $this->correctAnswerIndex);
                } elseif ($this->type === 'find_intruder') {
                    // Pour trouver l'intrus, l'intrus est is_correct = false
                    $isCorrect = ($index !== $this->intruderAnswerIndex);
                } else {
                    $isCorrect = $answer['is_correct'] ?? false;
                }

                $question->answers()->create([
                    'answer_text' => $answer['answer_text'],
                    'explanation' => $answer['explanation'] ?? '',
                    'is_correct' => $isCorrect,
                ]);
            }
        }

        session()->flash('success', 'La question et les réponses ont été ajoutées avec succès.');
        return redirect()->route('evaluations.show', $this->evaluationId);
    }

    public function render()
    {
        return view('livewire.question-form');
    }
}
