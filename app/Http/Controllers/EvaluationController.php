<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use Illuminate\Http\Request;
use App\Models\Formation;
use App\Models\Course;
use App\Models\Chapter;
use App\Models\Question;
use App\Models\Answer;

class EvaluationController extends Controller
{
    public function index()
    {
        $evaluations = Evaluation::all();

        $formationEvaluations = Evaluation::where('evaluatable_type', Formation::class)->get();
        $courseEvaluations = Evaluation::where('evaluatable_type', Course::class)->get();
        $chapterEvaluations = Evaluation::where('evaluatable_type', Chapter::class)->get();

        return view('authenticated.owners.evaluations.index', compact('evaluations', 'formationEvaluations', 'courseEvaluations', 'chapterEvaluations'));
    }

    public function create()
    {
        return view('authenticated.owners.evaluations.create');
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'model_type' => 'required|string|in:App\Models\Formation,App\Models\Course,App\Models\Chapter',
            'model_id' => 'required|integer',
        ]);

        // Créer une nouvelle évaluation
        $evaluation = new Evaluation([
            'title' => $validated['title'],
            'description' => $validated['description'],
        ]);

        // Récupérer le modèle associé
        $modelClass = $validated['model_type'];
        $model = $modelClass::findOrFail($validated['model_id']);

        // Vérifier si une évaluation existe déjà pour ce modèle
        if ($model->evaluation) {
            return redirect()->back()->withErrors(['model_id' => 'Une évaluation existe déjà pour ce modèle.']);
        }

        // Associer et sauvegarder l'évaluation
        $model->evaluation()->save($evaluation);

        // Rediriger vers la gestion des questions pour cette évaluation
        return redirect()->route('evaluations.show', $evaluation->id)
            ->with('success', 'Évaluation créée avec succès ! Veuillez maintenant ajouter des questions.')
            ->with('evaluatable_type', $validated['model_type'])
            ->with('evaluatable_id', $validated['model_id']);
    }

    public function show(Evaluation $evaluation)
    {
        return view('authenticated.owners.evaluations.show', compact('evaluation'));
    }

    public function edit(Evaluation $evaluation)
    {
        // $evaluation = Evaluation::findOrFail($id);

        // Récupérer les éléments associés selon le type d'évaluation
        $evaluatables = match ($evaluation->evaluatable_type) {
            'App\Models\Formation' => Formation::all(),
            'App\Models\Course' => Course::all(),
            'App\Models\Chapter' => Chapter::all(),
            default => []
        };

        return view('authenticated.owners.evaluations.edit', compact('evaluation', 'evaluatables'));
    }

    public function update(Request $request, Evaluation $evaluation)
    {
        // $evaluation = Evaluation::findOrFail($id);

        // Valider les données
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'model_type' => 'required|string',
            'model_id' => 'required|integer',
        ]);

        // Mettre à jour les données
        $evaluation->update([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'evaluatable_type' => $request->input('model_type'),
            'evaluatable_id' => $request->input('model_id'),
        ]);

        return redirect()->route('evaluations.show', $evaluation->id)
            ->with('success', 'L\'évaluation a été mise à jour avec succès.');
    }



    /**
     * Met à jour une réponse existante.
     */

    public function questionUpdate(Request $request, Question $question)
    {
        try {
            // Validation des données du formulaire
            $validated = $request->validate([
                'question_text' => 'required|string|max:255',
                // 'evaluation_id' => 'required|exists:evaluations,id', // Assurez-vous que evaluation_id existe dans la table evaluations
                // 'type' => 'required|string', // Ajout du type comme champ requis
            ]);

            // Mise à jour de la question avec les données validées
            $question->update($validated);

            // Redirection avec un message de succès
            return redirect()->back()->with('success', 'Évaluation mise à jour !');
        } catch (\Exception $e) {
            // Gestion des erreurs générales
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la mise à jour : ' . $e->getMessage());
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Gestion spécifique des erreurs de validation
            return redirect()->back()->withErrors($e->validator)->withInput();
        }
    }


    /**
     * Met à jour une réponse existante.
     */
    public function answerUpdate(Request $request, Answer $answer)
    {
        try {
            // Récupère uniquement les champs présents dans la requête
            $data = $request->only(['question_id', 'answer_text', 'is_correct']);

            // Valide uniquement les champs présents dans la requête
            $validated = $request->validate([
                'question_id' => 'sometimes|required|exists:questions,id', // Assurez-vous que question_id existe dans la table questions
                'answer_text' => 'sometimes|required|string|max:255',
                'is_correct' => 'sometimes|nullable|boolean',
            ]);

            // Filtre les champs pour ne mettre à jour que ceux qui ont changé
            $updatedData = array_filter($validated, function ($value, $key) use ($answer) {
                return $answer->{$key} !== $value;
            }, ARRAY_FILTER_USE_BOTH);

            // Si aucun champ n'a changé, rediriger sans mise à jour
            if (empty($updatedData)) {
                return redirect()->back()->with('info', 'Aucune modification détectée.');
            }

            // Mise à jour des champs modifiés
            $answer->update($updatedData);

            return redirect()->back()->with('success', 'Réponse mise à jour avec succès !');
        } catch (\Exception $e) {
            // Gestion des erreurs générales
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la mise à jour : ' . $e->getMessage());
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Gestion spécifique des erreurs de validation
            return redirect()->back()->withErrors($e->validator)->withInput();
        }
    }





    public function destroy(Evaluation $evaluation)
    {
        $evaluation->delete();

        return redirect()->route('evaluations.index')->with('success', 'Évaluation supprimée !');
    }
}
