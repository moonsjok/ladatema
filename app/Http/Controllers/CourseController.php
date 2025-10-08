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
