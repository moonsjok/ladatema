<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Evaluation;
use App\Models\Attempt;
use App\Models\Question;
use App\Models\Answer;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\WithPagination;
use Carbon\Carbon;

class EvaluationTake extends Component
{
    public Evaluation $evaluation;
    public $scoring_mode;
    public $passing_score;
    public $passed = false;
    public $pourcentage = 0;
    public Attempt $attempt;
    public $questions;
    public $currentQuestionIndex = 0;
    public $answers = [];
    public $timeRemaining = null;
    public $isSubmitted = false;
    public $timeSpent = 0;
    public $startTime;
    public $formattedTimeSpent = '00:00';
    
    #[Validate('required')]
    public $currentAnswer = null;
    
    protected $listeners = [
        'submitEvaluation' => 'submit'
    ];

    public function mount(Evaluation $evaluation, Attempt $attempt)
    {
        $this->evaluation = $evaluation;
        $this->scoring_mode = $evaluation->scoring_mode;
        $this->passing_score = $this->evaluation->passing_score ?? 60;
        $this->attempt = $attempt;
        $this->questions = $evaluation->questions()->with('answers')->get();
        
        // Initialiser le timer si l'évaluation a une durée
        if ($evaluation->duration) {
            $this->timeRemaining = $evaluation->duration * 60; // Convertir en secondes
        }
        
        // Enregistrer l'heure de début pour le calcul du temps
        $this->startTime = Carbon::now();
        
        // Créer la tentative si elle n'existe pas
        if (!$attempt->exists) {
            $attempt->evaluation_id = $evaluation->id;
            $attempt->user_id = auth()->id();
            $attempt->started_at = now();
            $attempt->save();
            
            $this->attempt = $attempt->fresh();
        }
    }

    public function goToQuestion($index)
    {
        $this->currentQuestionIndex = $index;
        $this->emitQuestionChanged();
    }

    public function nextQuestion()
    {
        if ($this->currentQuestionIndex < $this->questions->count() - 1) {
            $this->currentQuestionIndex++;
            $this->emitQuestionChanged();
        }
    }

    public function previousQuestion()
    {
        if ($this->currentQuestionIndex > 0) {
            $this->currentQuestionIndex--;
            $this->emitQuestionChanged();
        }
    }

    private function emitQuestionChanged()
    {

        // Restaurer la réponse précédemment sélectionnée pour cette question
        $currentQuestion = $this->questions[$this->currentQuestionIndex];
        $this->currentAnswer = $this->answers[$currentQuestion->id] ?? null;
        
               


        $this->dispatch('questionChanged', $this->currentQuestionIndex);
//   dd(
//          '  question: '. $this->questions
//          .'   questionIndex :'. $this->currentQuestionIndex
//          .' Answers '. print_r($this->answers) );
     }

    public function selectAnswer($answerId)
    {
        $currentQuestion = $this->questions[$this->currentQuestionIndex];
        
        // Gérer selon le type de question
        if ($currentQuestion->type === 'multiple_choice') {
           
            // Pour le choix multiple, gérer un tableau de réponses
            if (!isset($this->answers[$currentQuestion->id])) {
                $this->answers[$currentQuestion->id] = [];
            }
            
            $answerIndex = array_search($answerId, $this->answers[$currentQuestion->id]);
            
            if ($answerIndex !== false) {
                // Désélectionner la réponse
                unset($this->answers[$currentQuestion->id][$answerIndex]);
                $this->answers[$currentQuestion->id] = array_values($this->answers[$currentQuestion->id]);
            } else {
                // Sélectionner la réponse
                $this->answers[$currentQuestion->id][] = $answerId;
            }
            
            $this->currentAnswer = $this->answers[$currentQuestion->id];


        } elseif ($currentQuestion->type === 'text') {
            // Pour les questions texte, stocker directement la réponse texte
            $this->answers[$currentQuestion->id] = $answerId; // $answerId sera le texte ici
            $this->currentAnswer = $answerId;
        } else {
            // Pour choix unique et trouver l'intrus, garder la logique existante
            $this->answers[$currentQuestion->id] = $answerId;
            $this->currentAnswer = $answerId;
        }



        $this->dispatch('answerSelected', [
            'questionIndex' => $this->currentQuestionIndex,
            'answerId' => $answerId,
            'questionType' => $currentQuestion->type
        ]);
    }

    public function selectTextAnswer($text)
    {
        $currentQuestion = $this->questions[$this->currentQuestionIndex];
        if ($currentQuestion->type === 'text') {
            $this->answers[$currentQuestion->id] = $text;
            $this->currentAnswer = $text;
            
            $this->dispatch('answerSelected', [
                'questionIndex' => $this->currentQuestionIndex,
                'answerId' => $text,
                'questionType' => 'text'
            ]);
        }
    }

    public function submit()
    {
        if ($this->isSubmitted) {
            return;
        }

        // Vérifier si toutes les questions ont des réponses
        $unanswered = count($this->questions) - count($this->answers);
        
        if ($unanswered > 0) {
            $this->dispatch('showConfirmation', $unanswered);
            return;
        }

        $this->performSubmission();
    }

    public function confirmSubmit()
    {
        $this->performSubmission();
    }

    private function performSubmission()
    {
        if ($this->isSubmitted) {
            return;
        }

        $this->isSubmitted = true;
        
        // Dispatch l'événement de soumission
        $this->dispatch('evaluationSubmitted');

        // Calculer le score et déterminer si l'étudiant a réussi
        $scoreData = $this->calculateScore();
        $score = $scoreData['score'];
        $totalPoints = $scoreData['totalPoints'];
        $this->pourcentage = $scoreData['percentage'];
        $this->passed = $this->determineIfPassed($score, $totalPoints);

        // dd($score.' / '.$totalPoints.' Scorring mode '.$this->scoring_mode  .' Passing score  '. $this->evaluation->passing_score .'  passed '.$this->determineIfPassed($score, $totalPoints) );
        // dd( $this->timeSpent);

        // Mettre à jour la tentative
        $this->attempt->update([
            'completed_at' => now(),
            'score' => $score,
            'total_points' => $totalPoints,
            'pourcentage' => $this->pourcentage,
            'passed' => $this->passed,
            'time_spent' => $this->timeSpent,
            'answers_data' => $this->answers
        ]);

        // Sauvegarder les réponses individuelles
        foreach ($this->answers as $questionId => $answerId) {
            $question = $this->questions->find($questionId);
            
            // Gérer selon le type de question
            if ($question && $question->type === 'multiple_choice') {
                // Pour le choix multiple, créer un enregistrement par réponse sélectionnée


                if (is_array($answerId)) {
                    foreach ($answerId as $selectedAnswerId) {
                        $this->attempt->studentAnswers()->create([
                            'question_id' => $questionId,
                            'answer_id' => $selectedAnswerId,
                            'user_id' => auth()->id(),
                            'answered_at' => now()
                        ]);
                    }
                }
            } elseif ($question && $question->type === 'text') {
                // Pour les questions texte, stocker le texte directement (answer_id sera null)
                $this->attempt->studentAnswers()->create([
                    'question_id' => $questionId,
                    'answer_id' => null, // Pas de answer_id pour les questions texte
                    'user_id' => auth()->id(),
                    'answered_at' => now()
                ]);
            } else {
                // Pour choix unique et trouver l'intrus
                $this->attempt->studentAnswers()->create([
                    'question_id' => $questionId,
                    'answer_id' => $answerId,
                    'user_id' => auth()->id(),
                    'answered_at' => now()
                ]);
            }
        }

        // Rediriger vers les résultats
        return redirect()->route('student.evaluations.results', [
            $this->evaluation,
            $this->attempt
        ]);
    }

    /**
     * Mettre à jour le temps écoulé formaté
     */
    public function updateTimeSpent()
    {
        // Utiliser l'incrémentation directe du temps
        $this->timeSpent++;
        
        // Mettre à jour le temps restant si défini
        if ($this->timeRemaining !== null && $this->timeRemaining > 0) {
            $this->timeRemaining--;
            
            // Alerte quand il reste 5 minutes
            if ($this->timeRemaining === 300) {
                $this->dispatch('timeWarning', $this->timeRemaining);
            }
            
            // Alerte quand il reste 1 minute
            if ($this->timeRemaining === 60) {
                $this->dispatch('timeWarning', $this->timeRemaining);
            }
            
            if ($this->timeRemaining <= 0) {
                $this->dispatch('evaluationSubmitted');
                $this->performSubmission();
            }
        }
        
        // Formater le temps avec le helper
        $this->formattedTimeSpent = formateTime($this->timeSpent);
    }

    public function getCurrentQuestion()
    {
        return $this->questions[$this->currentQuestionIndex] ?? null;
    }

    public function getCurrentAnswer()
    {
        $currentQuestion = $this->getCurrentQuestion();
        if (!$currentQuestion) {
            return null;
        }
        
        $answer = $this->answers[$currentQuestion->id] ?? null;
        
        // Pour le choix multiple, retourner le tableau de réponses
        if ($currentQuestion->type === 'multiple_choice' && is_array($answer)) {
            return $answer;
        }
        
        return $answer;
    }

    public function isAnswerSelected($answerId)
    {
        $currentQuestion = $this->getCurrentQuestion();
        if (!$currentQuestion) {
            return false;
        }
        
        if ($currentQuestion->type === 'multiple_choice') {
            $selectedAnswers = $this->answers[$currentQuestion->id] ?? [];
            return in_array($answerId, $selectedAnswers);
        }
        
        return $this->answers[$currentQuestion->id] == $answerId;
    }

    public function getProgressPercentage()
    {
        return $this->questions->count() > 0 ? 
            round((($this->currentQuestionIndex + 1) / $this->questions->count()) * 100, 2) : 0;
    }

    public function getAnsweredCount()
    {
        return count($this->answers);
    }

    public function isQuestionAnswered($questionIndex)
    {
        $question = $this->questions[$questionIndex] ?? null;
        return $question && isset($this->answers[$question->id]);
    }

    /**
     * Calculer le score selon le mode de notation
     */
    private function calculateScore()
    {
        $score = 0;
        $totalPoints = 0;
        $totalCorrectAnswers = 0;

        if ($this->scoring_mode === "points") {
            // Mode points
            foreach ($this->questions as $question) {
                $totalPoints += $question->points;
                
                if (isset($this->answers[$question->id])) {
                    $questionScore = $this->calculateQuestionScore($question, $this->answers[$question->id]);
                    $score += $questionScore;
                }
            }
        } else {
            // Mode pourcentage
            foreach ($this->questions as $question) {
                if (isset($this->answers[$question->id])) {
                    $isCorrect = $this->isQuestionCorrect($question, $this->answers[$question->id]);
                    if ($isCorrect) {
                        $totalCorrectAnswers++;
                    }
                }
            }
            $score = $totalCorrectAnswers;
            $totalPoints = $this->questions->count();
        }

        $percentage = $totalPoints > 0 ? round(($score / $totalPoints) * 100, 0) : 0;

        return [
            'score' => $score,
            'totalPoints' => $totalPoints,
            'percentage' => $percentage
        ];
    }

    /**
     * Calculer le score pour une question spécifique
     */
    private function calculateQuestionScore($question, $answer)
    {
        if ($question->type === 'text') {
            // Pour les questions texte, la notation est manuelle ou basée sur des mots-clés
            // Pour l'instant, on considère que toute réponse non vide mérite les points
            return !empty($answer) ? $question->points : 0;
        }
        
        if ($question->type === 'multiple_choice') {
            if (!is_array($answer)) {
                return 0;
            }
            
            $correctAnswers = $question->answers()->where('is_correct', true)->pluck('id')->toArray();
            $selectedCorrect = 0;
            $selectedIncorrect = 0;
            
            foreach ($answer as $selectedAnswerId) {
                if (in_array($selectedAnswerId, $correctAnswers)) {
                    $selectedCorrect++;
                } else {
                    $selectedIncorrect++;
                }
            }
            
            // Calcul du score pour choix multiple
            $totalCorrect = count($correctAnswers);
            if ($totalCorrect === 0) return 0;
            
            // Score proportionnel : points si toutes les bonnes réponses sont sélectionnées et aucune mauvaise
            if ($selectedCorrect === $totalCorrect && $selectedIncorrect === 0) {
                return $question->points;
            } elseif ($selectedCorrect > 0 && $selectedIncorrect === 0) {
                // Points partiels si seulement des bonnes réponses sont sélectionnées
                return round(($selectedCorrect / $totalCorrect) * $question->points);
            } else {
                return 0; // Aucun point si des mauvaises réponses sont sélectionnées
            }
        }
        
        // Pour choix unique et trouver l'intrus
        $selectedAnswer = Answer::find($answer);
        return ($selectedAnswer && $selectedAnswer->is_correct) ? $question->points : 0;
    }

    /**
     * Vérifier si une question est correctement répondue
     */
    private function isQuestionCorrect($question, $answer)
    {
        if ($question->type === 'text') {
            // Pour les questions texte, considérer comme correct si non vide
            return !empty($answer);
        }
        
        if ($question->type === 'multiple_choice') {
            if (!is_array($answer)) {
                return false;
            }
            
            $correctAnswers = $question->answers()->where('is_correct', true)->pluck('id')->toArray();
            $selectedCorrect = 0;
            $selectedIncorrect = 0;
            
            foreach ($answer as $selectedAnswerId) {
                if (in_array($selectedAnswerId, $correctAnswers)) {
                    $selectedCorrect++;
                } else {
                    $selectedIncorrect++;
                }
            }
            
            $totalCorrect = count($correctAnswers);
            return ($selectedCorrect === $totalCorrect && $selectedIncorrect === 0);
        }
        
        // Pour choix unique et trouver l'intrus
        $selectedAnswer = Answer::find($answer);
        return ($selectedAnswer && $selectedAnswer->is_correct);
    }

    /**
     * Déterminer si l'étudiant a réussi selon le mode de notation
     */
    private function determineIfPassed($score, $totalPoints)
    {
        
        $passingScore = $this->evaluation->passing_score ?? 60;
        
        if ($this->scoring_mode === "points") {
            return $score >= $passingScore;
        } else {
            $percentage = $totalPoints > 0 ? round(($score / $totalPoints) * 100, 0) : 0;
            return $percentage >= $passingScore;
        }
    }

    /**
     * Obtenir le temps écoulé formaté pour l'affichage
     */
    public function getFormattedTimeSpent()
    {
        $seconds = $this->calculateTimeSpent();
        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;
        
        return sprintf("%02d:%02d", $minutes, $remainingSeconds);
    }

    public function render()
    {
        return view('livewire.evaluation-take');
    }
}
