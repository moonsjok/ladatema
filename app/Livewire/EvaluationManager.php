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
    public $newQuestion = ['type' => 'single_choice', 'text' => '', 'points' => 1];
    public $newAnswers = [];
    public $answerInput = '';

    public function mount($evaluatableType, $evaluatableId)
    {
        $this->evaluatableType = $evaluatableType;
        $this->evaluatableId = $evaluatableId;

        $this->evaluation = Evaluation::firstOrCreate(
            ['evaluatable_type' => $evaluatableType, 'evaluatable_id' => $evaluatableId],
            ['title' => 'Nouvelle évaluation', 'description' => '']
        );

        $this->refreshQuestions();
    }

    public function refreshQuestions()
    {
        $this->questions = $this->evaluation->questions()->with('answers')->get()->toArray();
    }

    public function addAnswer()
    {
        if (!empty(trim($this->answerInput))) {
            $this->newAnswers[] = [
                'text' => trim($this->answerInput),
                'is_correct' => false
            ];
            $this->answerInput = '';
        }
    }

    public function removeAnswer($index)
    {
        unset($this->newAnswers[$index]);
        $this->newAnswers = array_values($this->newAnswers);
    }

    public function toggleCorrectAnswer($index)
    {
        $questionType = $this->newQuestion['type'];
        
        if ($questionType === Question::TYPE_SINGLE_CHOICE) {
            // Pour le choix unique, une seule réponse peut être correcte
            foreach ($this->newAnswers as $i => &$answer) {
                $answer['is_correct'] = ($i === $index);
            }
        } elseif ($questionType === Question::TYPE_FIND_INTRUDER) {
            // Pour trouver l'intrus, une seule réponse est l'intrus (is_correct = false)
            foreach ($this->newAnswers as $i => &$answer) {
                $answer['is_correct'] = ($i !== $index);
            }
        } else {
            // Pour le choix multiple, plusieurs réponses peuvent être correctes
            $this->newAnswers[$index]['is_correct'] = !$this->newAnswers[$index]['is_correct'];
        }
    }

    public function addQuestion()
    {
        $this->validate([
            'newQuestion.text' => 'required|string|min:3',
            'newQuestion.type' => 'required|in:' . implode(',', [
                Question::TYPE_SINGLE_CHOICE,
                Question::TYPE_MULTIPLE_CHOICE,
                Question::TYPE_TEXT,
                Question::TYPE_FIND_INTRUDER
            ]),
            'newQuestion.points' => 'required|integer|min:1',
        ]);

        // Validation spécifique selon le type
        $questionType = $this->newQuestion['type'];
        
        if (in_array($questionType, [
            Question::TYPE_SINGLE_CHOICE, 
            Question::TYPE_MULTIPLE_CHOICE, 
            Question::TYPE_FIND_INTRUDER
        ])) {
            $this->validate([
                'newAnswers' => 'required|array|min:2',
                'newAnswers.*.text' => 'required|string|min:1'
            ]);

            // Vérifier qu'il y a au moins une réponse correcte
            $hasCorrectAnswer = collect($this->newAnswers)->contains('is_correct', true);
            if (!$hasCorrectAnswer && $questionType !== Question::TYPE_FIND_INTRUDER) {
                $this->addError('newAnswers', 'Au moins une réponse doit être marquée comme correcte.');
                return;
            }

            // Pour trouver l'intrus, vérifier qu'il y a exactement un intrus
            if ($questionType === Question::TYPE_FIND_INTRUDER) {
                $intruderCount = collect($this->newAnswers)->where('is_correct', false)->count();
                if ($intruderCount !== 1) {
                    $this->addError('newAnswers', 'Il doit y avoir exactement un intrus.');
                    return;
                }
            }
        }

        $question = $this->evaluation->questions()->create([
            'type' => $this->newQuestion['type'],
            'question_text' => $this->newQuestion['text'],
            'points' => $this->newQuestion['points']
        ]);

        // Créer les réponses seulement si le type le nécessite
        if ($question->requiresAnswers()) {
            foreach ($this->newAnswers as $answer) {
                $question->answers()->create([
                    'answer_text' => $answer['text'],
                    'is_correct' => $answer['is_correct'] ?? false,
                ]);
            }
        }

        // Réinitialiser le formulaire
        $this->reset(['newQuestion', 'newAnswers', 'answerInput']);
        $this->newQuestion = ['type' => 'single_choice', 'text' => '', 'points' => 1];
        
        $this->refreshQuestions();
    }

    public function deleteQuestion($questionId)
    {
        $question = Question::find($questionId);
        if ($question && $question->evaluation_id === $this->evaluation->id) {
            $question->delete();
            $this->refreshQuestions();
        }
    }

    public function getQuestionTypes()
    {
        return [
            Question::TYPE_SINGLE_CHOICE => 'Choix unique',
            Question::TYPE_MULTIPLE_CHOICE => 'Choix multiple',
            Question::TYPE_TEXT => 'Texte',
            Question::TYPE_FIND_INTRUDER => 'Trouver l\'intrus'
        ];
    }

    public function render()
    {
        return view('livewire.evaluation-manager', [
            'questions' => $this->questions,
            'questionTypes' => $this->getQuestionTypes()
        ]);
    }
}
