<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;

class CourseController extends Controller
{

    public function index()
    {
        return view('authenticated.owners.courses.index');
    }


    public function courseViewer(Course $course, $chapterId = null)
    {
        $user = auth()->user();

        // Pour les étudiants, vérifier la souscription et son statut d'expiration
        if ($user && $user->role === 'student') {
            $chapterIds = $course->chapters()->pluck('id')->toArray();

            $subscription = \App\Models\Subscription::where('user_id', $user->id)
                ->where(function ($query) use ($course, $chapterIds) {
                    $query->where('course_id', $course->id)
                          ->orWhere('formation_id', $course->formation_id);

                    if (!empty($chapterIds)) {
                        $query->orWhereIn('chapter_id', $chapterIds);
                    }
                })
                ->latest()
                ->first();

            if (!$subscription) {
                return redirect()->route('subscriptions.select')
                    ->with('error', 'Vous devez souscrire à cette formation pour y accéder.');
            }

            // Vérifier si la souscription a expiré (en utilisant strictement la colonne expires_at de la base de données)
            if ($subscription->isExpired()) {
                return redirect()->route('subscriptions.expired', $subscription->id);
            }

            // Vérifier si la souscription est en attente de validation
            if (!$subscription->is_validated) {
                return redirect()->route('dashboard')
                    ->with('info', 'Votre souscription est en attente de validation par l\'administration.');
            }
        }

        // Récupérer le chapitre si l'ID est fourni
        $chapter = $chapterId ? $course->chapters()->find($chapterId) : null;

        // Passer les données à la vue
        return view('authenticated.students.courses.view', [
            'course' => $course,
            'chapterId' => $chapterId,
        ]);
    }


    public function evaluation(Course $course)
    {
        $evaluation = $course->evaluation; // Relation morphique
        $questions = $evaluation ? $evaluation->questions()->with('answers')->get() : [];

        return view('authenticated.owners.courses.evaluation', compact('course', 'evaluation', 'questions'));
    }
}
