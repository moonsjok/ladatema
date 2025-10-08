<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Formation;

class FormationController extends Controller
{

    /**Guest methods */



    /** Authenticated formaion */
    public function index()
    {
        return view('authenticated.owners.formations.index');
    }
    public function quickCreate()
    {
        return view('authenticated.owners.formations.quickCreate');
    }

    public function evaluation(Formation $formation)
    {
        $evaluation = $formation->evaluation; // Relation morphique
        $questions = $evaluation ? $evaluation->questions()->with('answers')->get() : [];

        return view('authenticated.owners.formations.evaluation', compact('formation', 'evaluation', 'questions'));
    }
}
