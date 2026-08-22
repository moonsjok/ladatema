<?php

namespace App\Livewire;

use App\Models\Question;
use App\Models\Answer;
use Livewire\Component;

class QuestionEditForm extends Component
{
    public $evaluationId;
    public $questionText;
    public $type = 'single_choice';
    public $answers = [];
    public $correctAnswerIndex = null;
    public $intruderAnswerIndex = null;
    public $points = 1;
    public $currentQuestionId = null;
    public $editingQuestionId = null;
    public $editingQuestion = null;

    protected $rules = [
        'questionText' => 'required|string|max:255',
        'type' => 'required|in:single_choice,multiple_choice,text,find_intruder',
        'points' => 'required|integer|min:1|max:100',
        'answers.*.answer_text' => 'required|string|max:255',
        'answers.*.is_correct' => 'nullable|boolean',
    ];

    public function mount($evaluationId, $questionId)
    {
        $this->evaluationId = $evaluationId;
        $this->currentQuestionId = $questionId;

        // Charger les données de la question
        $this->loadQuestionData($questionId);
    }

    public function loadQuestionData($questionId)
    {
        $this->editingQuestionId = $questionId;
        $this->editingQuestion = Question::with('answers')->findOrFail($questionId);

        $this->questionText = $this->editingQuestion->question_text;
        $this->type = $this->editingQuestion->type;
        $this->points = $this->editingQuestion->points;

        $this->answers = [];
        if ($this->editingQuestion->answers->count() > 0) {
            foreach ($this->editingQuestion->answers as $answer) {
                $this->answers[] = [
                    'answer_text' => $answer->answer_text,
                    'explanation' => $answer->explanation ?? '',
                    'is_correct' => $answer->is_correct,
                ];

                if ($this->type === 'single_choice' && $answer->is_correct) {
                    $this->correctAnswerIndex = count($this->answers) - 1;
                } elseif ($this->type === 'find_intruder' && !$answer->is_correct) {
                    $this->intruderAnswerIndex = count($this->answers) - 1;
                }
            }
        } else {
            $this->answers = [
                ['answer_text' => '', 'explanation' => '', 'is_correct' => false],
                ['answer_text' => '', 'explanation' => '', 'is_correct' => false],
            ];
        }
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

        // Conserver les réponses existantes mais adapter leur format selon le nouveau type
        if ($value === 'text') {
            // Pour le type texte, on vide les réponses
            $this->answers = [];
        } else {
            // Adapter les réponses existantes au nouveau type
            foreach ($this->answers as &$answer) {
                // Réinitialiser l'état correct pour le nouveau type
                $answer['is_correct'] = false;
            }

            // S'assurer qu'il y a au moins 2 réponses
            if (count($this->answers) < 2) {
                $this->answers[] = ['answer_text' => '', 'explanation' => '', 'is_correct' => false];
            }
        }
    }


    // Méthodes spécifiques pour les changements de propriétés imbriquées

    public function updatedCorrectAnswerIndex($value)
    {
        // Mettre à jour l'état des réponses pour le type choix unique
        if ($this->type === 'single_choice' && $value !== null) {
            foreach ($this->answers as $index => &$answer) {
                $answer['is_correct'] = ($index === $value);
            }
        }
    }

    public function updatedIntruderAnswerIndex($value)
    {
        // Mettre à jour l'état des réponses pour le type trouver l'intrus
        if ($this->type === 'find_intruder' && $value !== null) {
            foreach ($this->answers as $index => &$answer) {
                // Pour trouver l'intrus, l'intrus est is_correct = false
                $answer['is_correct'] = ($index !== $value);
            }
        }
    }

    // Méthode pour gérer les changements de texte des réponses
    public function updatedAnswersAnswerText($value, $key)
    {
        // Extraire l'index depuis la clé (ex: "answers.0.answer_text")
        $parts = explode('.', $key);
        $index = (int)$parts[1];

        if (isset($this->answers[$index])) {
            $this->answers[$index]['answer_text'] = $value;
        }
    }

    // Méthode pour gérer les changements d'explication des réponses
    public function updatedAnswersExplanation($value, $key)
    {
        // Extraire l'index depuis la clé (ex: "answers.0.explanation")
        $parts = explode('.', $key);
        $index = (int)$parts[1];

        if (isset($this->answers[$index])) {
            $this->answers[$index]['explanation'] = $value;
        }
    }

    // Méthode pour gérer les changements de is_correct (pour choix multiple)
    public function updatedAnswersIsCorrect($value, $key)
    {
        // Extraire l'index depuis la clé (ex: "answers.0.is_correct")
        $parts = explode('.', $key);
        $index = (int)$parts[1];

        if (isset($this->answers[$index])) {
            $this->answers[$index]['is_correct'] = $value;

            // Pour le choix unique, synchroniser avec correctAnswerIndex
            if ($this->type === 'single_choice' && $value) {
                $this->correctAnswerIndex = $index;
                // Réinitialiser les autres réponses
                foreach ($this->answers as $i => &$answer) {
                    if ($i !== $index) {
                        $answer['is_correct'] = false;
                    }
                }
            } elseif ($this->type === 'find_intruder' && !$value) {
                $this->intruderAnswerIndex = $index;
                // Réinitialiser les autres réponses (toutes correctes sauf l'intrus)
                foreach ($this->answers as $i => &$answer) {
                    $answer['is_correct'] = ($i !== $index);
                }
            }
        }
    }

    // Méthode pour synchroniser manuellement les états
    public function syncAnswerStates()
    {
        if ($this->type === 'single_choice' && $this->correctAnswerIndex !== null) {
            foreach ($this->answers as $index => &$answer) {
                $answer['is_correct'] = ($index === $this->correctAnswerIndex);
            }
        } elseif ($this->type === 'find_intruder' && $this->intruderAnswerIndex !== null) {
            foreach ($this->answers as $index => &$answer) {
                $answer['is_correct'] = ($index !== $this->intruderAnswerIndex);
            }
        }
        // Pour le choix multiple, on ne fait rien (les checkbox gèrent elles-mêmes)
    }

    public function save()
    {
        try {
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

            // Mettre à jour la question existante
            $this->editingQuestion->update([
                'type' => $this->type,
                'question_text' => $this->questionText,
                'points' => $this->points,
            ]);

            // Supprimer les anciennes réponses et en créer de nouvelles
            $this->editingQuestion->answers()->delete();

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

                    $this->editingQuestion->answers()->create([
                        'answer_text' => $answer['answer_text'],
                        'explanation' => $answer['explanation'] ?? '',
                        'is_correct' => $isCorrect,
                    ]);
                }
            }

            // Envoyer l'événement de succès avec SweetAlert
            $this->dispatch('questionUpdated', 'La question a été mise à jour avec succès!');
        } catch (\Exception $e) {
            // Envoyer l'événement d'erreur avec SweetAlert
            $this->dispatch('questionUpdateError', 'Une erreur est survenue lors de la mise à jour: ' . $e->getMessage());
        }
    }

    public function cancelEdit()
    {
        $this->dispatch('closeModal');
    }

    public function render()
    {
        return view('livewire.question-edit-form');
    }
}
