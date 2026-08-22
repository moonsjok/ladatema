<?php

namespace App\Http\Controllers;

use App\Models\Attempt;
use Illuminate\Http\Request;

class AttemptController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Récupérer les paramètres de tri
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $filterStudent = $request->get('filter_student');
        $filterPassed = $request->get('filter_passed');
        $filterEvaluation = $request->get('filter_evaluation');

        // Construire la requête avec les relations
        $query = Attempt::with(['user', 'evaluation']);

        // Filtrer par étudiant
        if ($filterStudent) {
            $query->whereHas('user', function ($q) use ($filterStudent) {
                $q->where('name', 'like', '%' . $filterStudent . '%')
                    ->orWhere('email', 'like', '%' . $filterStudent . '%');
            });
        }

        // Filtrer par réussite
        if ($filterPassed !== null && $filterPassed !== '') {
            $query->where('passed', $filterPassed === '1' ? true : false);
        }

        // Filtrer par évaluation
        if ($filterEvaluation) {
            $query->whereHas('evaluation', function ($q) use ($filterEvaluation) {
                $q->where('title', 'like', '%' . $filterEvaluation . '%');
            });
        }

        // Appliquer le tri
        if ($sortBy === 'student_name') {
            $query->join('users', 'attempts.user_id', '=', 'users.id')
                ->orderBy('users.name', $sortOrder)
                ->select('attempts.*');
        } elseif ($sortBy === 'evaluation_title') {
            $query->join('evaluations', 'attempts.evaluation_id', '=', 'evaluations.id')
                ->orderBy('evaluations.title', $sortOrder)
                ->select('attempts.*');
        } elseif ($sortBy === 'score') {
            $query->orderBy('pourcentage', $sortOrder);
        } elseif ($sortBy === 'passed') {
            $query->orderBy('passed', $sortOrder);
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Paginer les résultats
        $attempts = $query->paginate(15)->withQueryString();

        // Statistiques
        $stats = [
            'total' => Attempt::count(),
            'passed' => Attempt::where('passed', true)->count(),
            'failed' => Attempt::where('passed', false)->count(),
            'average_score' => Attempt::avg('pourcentage'),
        ];

        return view('authenticated.owners.attempts.index', compact('attempts', 'stats', 'sortBy', 'sortOrder', 'filterStudent', 'filterPassed', 'filterEvaluation'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Attempt $attempt)
    {
        // Charger toutes les relations nécessaires
        $attempt->load([
            'user',
            'evaluation.evaluatable',
            'studentAnswers.question.answers',
            'studentAnswers.answer'
        ]);

        // Calculer les statistiques détaillées
        $stats = [
            'total_questions' => $attempt->evaluation->questions->count(),
            'answered_questions' => $attempt->studentAnswers->count(),
            'correct_answers' => $attempt->studentAnswers->filter(function ($answer) {
                return $answer->isCorrect();
            })->count(),
            'incorrect_answers' => $attempt->studentAnswers->filter(function ($answer) {
                return !$answer->isCorrect();
            })->count(),
            'time_per_question' => $attempt->time_spent > 0 && $attempt->studentAnswers->count() > 0
                ? round($attempt->time_spent / $attempt->studentAnswers->count(), 2)
                : 0,
        ];

        // Grouper les réponses par type de question
        $answersByType = $attempt->studentAnswers->groupBy(function ($answer) {
            return $answer->question->getTypeLabel();
        });

        return view('authenticated.owners.attempts.show', compact('attempt', 'stats', 'answersByType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attempt $attempt)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Attempt $attempt)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attempt $attempt)
    {
        //
    }
}
