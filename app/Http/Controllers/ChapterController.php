<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Course;
use App\Models\Chapter;


class ChapterController extends Controller
{
    /**
     * Affiche la liste des chapitres du cours sélectionné.
     */
    public function index(Request $request)
    {
        $courses = Course::all(); // Liste de tous les cours
        $selectedCourseId = $request->query('course_id'); // ID du cours sélectionné

        $chapters = [];
        if ($selectedCourseId) {
            $chapters = Chapter::where('course_id', $selectedCourseId)->orderBy('numero')->get();
        }

        return view('authenticated.owners.chapters.index', compact('courses', 'chapters', 'selectedCourseId'));
    }

    /**
     * Affiche le formulaire de création d'un chapitre pour le cours sélectionné.
     */
    public function create(Request $request)
    {
        $selectedCourseId = $request->query('course_id');
        $courses = Course::all();

        return view('authenticated.owners.chapters.create', compact('courses', 'selectedCourseId'));
    }

    /**
     * Stocke un nouveau chapitre en base de données.
     */
    /**
     * Store a newly created chapter in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'numero' => 'required|regex:/^\d+(\.\d+)*$/',
            'title' => 'required|string|max:255',
            'content' => 'required',
            'course_id' => 'required|exists:courses,id',
        ]);

        if ($validator->fails()) {
            return redirect()->route('chapters.create', ['course_id' => $request->input('course_id')])
                ->withErrors($validator)
                ->withInput();
        }

        Chapter::create($request->all());

        return redirect()->route('chapters.index', ['course_id' => $request->input('course_id')])
            ->with('success', 'Chapitre créé avec succès.');
    }



    /**
     * Display the specified chapter.
     *
     * @param  \App\Models\Chapter  $chapter
     * @return \Illuminate\View\View
     */
    public function show(Chapter $chapter)
    {
        return view('authenticated.owners.chapters.show', compact('chapter'));
    }

    /**
     * Show the form for editing the specified chapter.
     *
     * @param  \App\Models\Chapter  $chapter
     * @return \Illuminate\View\View
     */
    public function edit(Chapter $chapter)
    {
        $data = [
            'chapters' => Chapter::all(),
            'courses' => Course::all(),
            'chapter' => $chapter
        ];
        return view('authenticated.owners.chapters.edit', $data);
    }

    /**
     * Update the specified chapter in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Chapter  $chapter
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Chapter $chapter)
    {
        $validator = Validator::make($request->all(), [
            'numero' => 'required|regex:/^\d+(\.\d+)*$/',
            'title' => 'required|string|max:255',
            'content' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->route('chapters.edit', [$chapter->id, "course_id" => $request->course_id])
                ->withErrors($validator)
                ->withInput();
        }

        $chapter->update($request->all());

        return redirect()->route('chapters.index')
            ->with('success', 'Chapitre mis à jour avec succès.');
    }

    /**
     * Remove the specified chapter from storage (soft delete).
     *
     * @param  \App\Models\Chapter  $chapter
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Chapter $chapter)
    {
        $chapter->delete();
        return redirect()->route('authenticated.owners.chapters.index')
            ->with('success', 'Chapitre supprimé avec succès.');
    }

    /**
     * Restore the specified chapter from soft delete.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore($id)
    {
        $chapter = Chapter::withTrashed()->findOrFail($id);
        $chapter->restore();
        return redirect()->route('authenticated.owners.chapters.index')
            ->with('success', 'Chapitre restauré avec succès.');
    }

    /**
     * Display a listing of the trashed chapters.
     *
     * @return \Illuminate\View\View
     */
    public function trashed()
    {
        $trashedChapters = Chapter::onlyTrashed()->with('chapter')->get();
        return view('authenticated.owners.chapters.trashed', compact('trashedChapters'));
    }

    public function evaluation(Chapter $chapter)
    {
        $evaluation = $chapter->evaluation; // Relation morphique
        $questions = $evaluation ? $evaluation->questions()->with('answers')->get() : [];

        return view('authenticated.owners.chapters.evaluation', compact('chapter', 'evaluation', 'questions'));
    }
}
