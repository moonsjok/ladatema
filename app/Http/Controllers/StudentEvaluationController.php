<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\Attempt;
use App\Models\StudentAnswer;
use App\Models\Question;
use App\Models\Answer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentEvaluationController extends Controller
{
    /**
     * Afficher la liste des évaluations accessibles à l'étudiant
     */
    public function index()
    {
        $user = Auth::user();
        
        // Récupérer toutes les formations auxquelles l'étudiant est souscrit
        $subscriptions = $user->souscriptions()->with('formation')->get();
        
        $evaluations = collect();
        
        foreach ($subscriptions as $subscription) {
            $formation = $subscription->formation;
            
            // Récupérer les évaluations de la formation
            $formationEvaluations = Evaluation::where('evaluatable_type', 'App\Models\Formation')
                ->where('evaluatable_id', $formation->id)
                ->get();
            
            // Récupérer les évaluations des cours de la formation
            $courseEvaluations = Evaluation::where('evaluatable_type', 'App\Models\Course')
                ->whereIn('evaluatable_id', $formation->courses()->pluck('id'))
                ->get();
            
            // Récupérer les évaluations des chapitres de la formation (via les cours)
            $allChapterIds = $formation->courses()->with('chapters')->get()
                ->flatMap(function ($course) {
                    return $course->chapters->pluck('id');
                });
            
            $chapterEvaluations = Evaluation::where('evaluatable_type', 'App\Models\Chapter')
                ->whereIn('evaluatable_id', $allChapterIds)
                ->get();
            
            // Fusionner toutes les évaluations
            $allEvaluations = $formationEvaluations->merge($courseEvaluations)->merge($chapterEvaluations);
            $evaluations = $evaluations->merge($allEvaluations);
        }
        
        // Éliminer les doublons et trier
        $evaluations = $evaluations->unique('id')->sortBy('created_at');
        
        return view('authenticated.students.evaluations.index', compact('evaluations'));
    }
    
    /**
     * Afficher les détails d'une évaluation
     */
    public function show(Evaluation $evaluation)
    {
        $user = Auth::user();
        
        // Vérifier si l'étudiant a accès à cette évaluation
        if (!$this->hasAccess($user, $evaluation)) {
            abort(403, 'Vous n\'avez pas accès à cette évaluation.');
        }
        
        // Vérifier les tentatives précédentes
        $attempts = Attempt::where('evaluation_id', $evaluation->id)
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('authenticated.students.evaluations.show', compact('evaluation', 'attempts'));
    }
    
    /**
     * Commencer une évaluation
     */
    public function start(Evaluation $evaluation)
    {
        $user = Auth::user();
        
        // Vérifier si l'étudiant a accès à cette évaluation
        if (!$this->hasAccess($user, $evaluation)) {
            abort(403, 'Vous n\'avez pas accès à cette évaluation.');
        }
        
        // Vérifier le nombre de tentatives déjà effectuées
        $attemptCount = Attempt::where('evaluation_id', $evaluation->id)
            ->where('user_id', $user->id)
            ->count();
            
        // Vérifier si l'étudiant peut encore tenter
        if (!empty($evaluation->max_attempts) && intval(-$attemptCount) >= $evaluation->max_attempts) {
            return back()->with('error', 'Vous avez atteint le nombre maximum de tentatives autorisées.');
        }
        
        // Créer une nouvelle tentative
        $attempt = Attempt::create([
            'evaluation_id' => $evaluation->id,
            'user_id' => $user->id,
            'score' => 0,
            'total_points' => 0,
            'pourcentage' => 0,
            'grade' => 'F',
            'passed' => false,
            'started_at' => now(),
        ]);
        
        // Récupérer les questions aléatoires
        $questions = $this->getRandomQuestions($evaluation);
        
        return view('authenticated.students.evaluations.take', compact('evaluation', 'attempt', 'questions'));
    }
    
    /**
     * Soumettre les réponses d'une évaluation
     */
    public function submit(Request $request, Evaluation $evaluation, Attempt $attempt)
    {
        $user = Auth::user();
        
        // Vérifier si l'étudiant a accès à cette évaluation
        if (!$this->hasAccess($user, $evaluation)) {
            abort(403, 'Vous n\'avez pas accès à cette évaluation.');
        }
        
        // Vérifier si la tentative appartient à l'utilisateur
        if ($attempt->user_id !== $user->id) {
            abort(403, 'Cette tentative ne vous appartient pas.');
        }
        
        // Vérifier si la tentative n'est pas déjà terminée
        if ($attempt->completed_at) {
            abort(403, 'Cette tentative est déjà terminée.');
        }
        
        $answers = $request->input('answers', []);
        
        DB::beginTransaction();
        try {
            // Sauvegarder les réponses de l'étudiant
            foreach ($answers as $questionId => $answerId) {
                StudentAnswer::create([
                    'attempt_id' => $attempt->id,
                    'question_id' => $questionId,
                    'answer_id' => $answerId,
                    'user_id' => $user->id,
                    'answered_at' => now(),
                ]);
            }
            
            // Calculer le score
            $score = $this->calculateScore($attempt);
            
            // Mettre à jour la tentative
            $attempt->update([
                'score' => $score['score'],
                'total_points' => $score['total_points'],
                'pourcentage' => $score['pourcentage'],
                'grade' => $score['grade'],
                'passed' => $score['passed'],
                'completed_at' => now(),
                'time_spent' => $attempt->started_at->diffInSeconds(now()),
            ]);
            
            DB::commit();
            
            return redirect()->route('student.evaluations.results', [$evaluation, $attempt])
                ->with('success', 'Évaluation terminée avec succès !');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Une erreur est survenue lors de la soumission.');
        }
    }
    
    /**
     * Afficher les résultats d'une tentative
     */
    public function results(Evaluation $evaluation, Attempt $attempt)
    {
        $user = Auth::user();
        
        // Vérifier si l'étudiant a accès à cette évaluation
        if (!$this->hasAccess($user, $evaluation)) {
            abort(403, 'Vous n\'avez pas accès à cette évaluation.');
        }
        
        // Vérifier si la tentative appartient à l'utilisateur
        if ($attempt->user_id !== $user->id) {
            abort(403, 'Cette tentative ne vous appartient pas.');
        }
        
        // Charger les réponses de l'étudiant avec les questions et réponses correctes
        $attempt->load(['studentAnswers.question.answers', 'studentAnswers.answer']);
        
        return view('authenticated.students.evaluations.results', compact('evaluation', 'attempt'));
    }
    
    /**
     * Vérifier si l'étudiant a accès à l'évaluation
     */
    private function hasAccess($user, $evaluation)
    {
        // Récupérer les formations souscrites par l'étudiant
        $subscriptions = $user->souscriptions()->with('formation')->get();
        
        foreach ($subscriptions as $subscription) {
            $formation = $subscription->formation;
            
            // Vérifier si l'évaluation est sur la formation
            if ($evaluation->evaluatable_type === 'App\Models\Formation' && 
                $evaluation->evaluatable_id === $formation->id) {
                return true;
            }
            
            // Vérifier si l'évaluation est sur un cours de la formation
            if ($evaluation->evaluatable_type === 'App\Models\Course') {
                $courseIds = $formation->courses()->pluck('id');
                if ($courseIds->contains($evaluation->evaluatable_id)) {
                    return true;
                }
            }
            
            // Vérifier si l'évaluation est sur un chapitre de la formation
            if ($evaluation->evaluatable_type === 'App\Models\Chapter') {
                $chapterIds = $formation->chapters()->pluck('id');
                if ($chapterIds->contains($evaluation->evaluatable_id)) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Récupérer les questions aléatoires pour une évaluation
     */
    private function getRandomQuestions(Evaluation $evaluation)
    {
        $allQuestions = $evaluation->questions()->with('answers')->get();
        
        // Si total_questions est spécifié, prendre ce nombre aléatoirement
        if ($evaluation->total_questions) {
            return $allQuestions->random(min($evaluation->total_questions, $allQuestions->count()));
        }
        
        // Sinon, prendre toutes les questions
        return $allQuestions;
    }
    
    /**
     * Calculer le score d'une tentative
     */
    private function calculateScore(Attempt $attempt)
    {
        $studentAnswers = $attempt->studentAnswers()->with('answer')->get();
        
        $totalPoints = 0;
        $correctPoints = 0;
        
        foreach ($studentAnswers as $studentAnswer) {
            $question = $studentAnswer->question;
            $totalPoints += $question->points;
            
            if ($studentAnswer->isCorrect()) {
                $correctPoints += $question->points;
            }
        }
        
        // Calculer le pourcentage
        $pourcentage = $totalPoints > 0 ? ($correctPoints / $totalPoints) * 100 : 0;
        
        // Déterminer le grade
        $grade = 'F';
        if ($pourcentage >= 90) $grade = 'A+';
        elseif ($pourcentage >= 80) $grade = 'A';
        elseif ($pourcentage >= 70) $grade = 'B';
        elseif ($pourcentage >= 60) $grade = 'C';
        
        // Déterminer si réussi
        $passed = false;
        if ($evaluation->scoring_mode === 'pourcentage') {
            $passed = $pourcentage >= ($evaluation->passing_score ?? 70);
        } else {
            $passed = $correctPoints >= ($evaluation->passing_score ?? 0);
        }
        
        return [
            'score' => $correctPoints,
            'total_points' => $totalPoints,
            'pourcentage' => round($pourcentage, 2),
            'grade' => $grade,
            'passed' => $passed,
        ];
    }
}
