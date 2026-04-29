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
        // Rediriger vers le nouveau processus par étapes
        return redirect()->route('evaluations.create.step1');
    }

    // Étape 1 : Sélection du type d'évaluation
    public function createStep1()
    {
        return view('authenticated.owners.evaluations.create-step-1');
    }

    public function storeStep1(Request $request)
    {
        $validated = $request->validate([
            'model_type' => 'required|string|in:App\Models\Formation,App\Models\Course,App\Models\Chapter',
        ]);

        // Stocker en session pour l'étape suivante
        session(['evaluation_creation' => [
            'model_type' => $validated['model_type']
        ]]);

        return redirect()->route('evaluations.create.step2');
    }

    // Étape 2 : Sélection de l'élément avec recherche
    public function createStep2()
    {
        if (!session()->has('evaluation_creation.model_type')) {
            return redirect()->route('evaluations.create.step1');
        }

        $modelType = session('evaluation_creation.model_type');
        $modelClass = $modelType;
        
        // Récupérer tous les éléments pour ce type avec gestion spécifique pour les chapitres
        if ($modelType === 'App\Models\Chapter') {
            // Les chapitres n'ont pas de colonne description, seulement content
            $items = $modelClass::select('id', 'title')->paginate(10);
            
            // Ajouter une description vide pour chaque chapitre pour éviter les erreurs dans la vue
            $items->getCollection()->transform(function ($item) {
                $item->description = ''; // Initialiser avec une description vide
                return $item;
            });
        } else {
            // Formations et cours ont une colonne description
            $items = $modelClass::select('id', 'title', 'description')->paginate(10);
        }
        
        return view('authenticated.owners.evaluations.create-step-2', compact('items', 'modelType'));
    }

    public function storeStep2(Request $request)
    {
        $validated = $request->validate([
            'model_id' => 'required|integer',
        ]);

        // Mettre à jour la session
        $sessionData = session('evaluation_creation');
        $sessionData['model_id'] = $validated['model_id'];
        session(['evaluation_creation' => $sessionData]);

        return redirect()->route('evaluations.create.step3');
    }

    // Étape 3 : Détails de l'évaluation
    public function createStep3()
    {
        if (!session()->has('evaluation_creation.model_id')) {
            return redirect()->route('evaluations.create.step1');
        }

        $sessionData = session('evaluation_creation');
        $modelClass = $sessionData['model_type'];
        $model = $modelClass::findOrFail($sessionData['model_id']);
        
        // Gérer spécifiquement les chapitres qui n'ont pas de description
        if ($sessionData['model_type'] === 'App\Models\Chapter') {
            // Ajouter une description vide si elle n'existe pas
            if (!isset($model->description)) {
                $model->description = ''; // Description vide pour les chapitres
            }
        }
        
        return view('authenticated.owners.evaluations.create-step-3', compact('model', 'sessionData'));
    }

    public function storeStep3(Request $request)
    {
        $sessionData = session('evaluation_creation');
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scoring_mode' => 'nullable|string|in:pourcentage,points',
            'passing_score' => 'nullable|integer|min:0|max:100',
            'duration' => 'nullable|integer|min:1',
            'total_questions' => 'nullable|integer|min:1',
            'max_attempts' => 'nullable|integer|min:1|max:10',
            'importance' => 'required|string|in:mandatory,optional',
        ]);

        // Créer l'évaluation avec les valeurs par défaut
        $evaluation = new Evaluation([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'scoring_mode' => $validated['scoring_mode'] ?? 'pourcentage',
            'passing_score' => $validated['passing_score'] ?? 60,
            'duration' => $validated['duration'],
            'total_questions' => $validated['total_questions'],
            'max_attempts' => $validated['max_attempts'] ?? NULL,
            'can_reset' => $validated['can_reset'] ?? true,
            'importance' => $validated['importance'],
        ]);

        // Récupérer le modèle associé
        $modelClass = $sessionData['model_type'];
        $model = $modelClass::findOrFail($sessionData['model_id']);

        // Vérifier si une évaluation existe déjà
        if ($model->evaluation) {
            return redirect()->back()->withErrors(['title' => 'Une évaluation existe déjà pour ce modèle.']);
        }

        // Associer et sauvegarder
        $evaluation->evaluatable()->associate($model);
        $evaluation->save();

        // Nettoyer la session
        session()->forget('evaluation_creation');

        return redirect()->route('evaluations.show', $evaluation->id)
            ->with('success', 'Évaluation créée avec succès ! Veuillez maintenant ajouter des questions.');
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
        $evaluation->evaluatable()->associate($model);
        $evaluation->save();

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

    /**
     * Met à jour une réponse existante.
     */

    public function questionUpdate(Request $request, Question $question)
    {
        try {
            // Validation des données du formulaire
            $validated = $request->validate([
                'question_text' => 'required|string|max:255',
                'type' => 'required|string|in:single_choice,multiple_choice,text,find_intruder',
                'points' => 'required|integer|min:1|max:100',
            ]);

            $oldType = $question->type;
            $newType = $validated['type'];

            // Mise à jour de la question avec les données validées
            $question->update([
                'question_text' => $validated['question_text'],
                'type' => $newType,
                'points' => $validated['points'],
            ]);

            // Gérer la conversion des réponses si le type change
            if ($oldType !== $newType) {
                if ($newType === 'text') {
                    // Conversion vers texte : conserver les réponses existantes mais les marquer comme non utilisées
                    $question->answers()->update(['is_correct' => false]);
                } elseif ($oldType === 'text') {
                    // Conversion depuis texte : créer des réponses par défaut
                    $question->answers()->delete();
                    $defaultAnswers = [
                        ['answer_text' => 'Réponse A', 'is_correct' => true],
                        ['answer_text' => 'Réponse B', 'is_correct' => false],
                        ['answer_text' => 'Réponse C', 'is_correct' => false],
                    ];
                    
                    if ($newType === 'find_intruder') {
                        // Pour trouver l'intrus, la première réponse est l'intrus (is_correct = false)
                        $defaultAnswers[0]['is_correct'] = false;
                        $defaultAnswers[1]['is_correct'] = true;
                        $defaultAnswers[2]['is_correct'] = true;
                    }
                    
                    foreach ($defaultAnswers as $answer) {
                        $question->answers()->create($answer);
                    }
                } else {
                    // Conversion entre types à choix : adapter les réponses existantes
                    if ($newType === 'find_intruder') {
                        // Pour trouver l'intrus, inverser la logique
                        $correctAnswers = $question->answers()->where('is_correct', true)->get();
                        if ($correctAnswers->count() > 0) {
                            // Prendre la première réponse correcte et la rendre intruse
                            $firstCorrect = $correctAnswers->first();
                            $firstCorrect->update(['is_correct' => false]);
                        }
                    } elseif ($oldType === 'find_intruder') {
                        // Conversion depuis trouver l'intrus vers un autre type
                        $intruderAnswer = $question->answers()->where('is_correct', false)->first();
                        if ($intruderAnswer) {
                            $intruderAnswer->update(['is_correct' => true]);
                        }
                    }
                }
            }

            // Redirection avec un message de succès
            return redirect()->back()->with('success', 'Question mise à jour avec succès !');
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
            $data = $request->only(['question_id', 'answer_text', 'explanation', 'is_correct']);

            // Valide uniquement les champs présents dans la requête
            $validated = $request->validate([
                'question_id' => 'sometimes|required|exists:questions,id', // Assurez-vous que question_id existe dans la table questions
                'answer_text' => 'sometimes|required|string|max:255',
                'explanation' => 'sometimes|nullable|string',
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

// ===================================
// MÉTHODES D'ÉDITION PAR ÉTAPES
// ===================================

    // Étape 1 : Modifier le type d'évaluation
    public function editStep1(Evaluation $evaluation)
    {
        // Stocker l'évaluation en session pour les étapes suivantes
        session(['evaluation_edit' => [
            'evaluation_id' => $evaluation->id,
            'current_model_type' => $evaluation->evaluatable_type,
            'current_model_id' => $evaluation->evaluatable_id
        ]]);

        return view('authenticated.owners.evaluations.edit-step-1', compact('evaluation'));
    }

    public function updateStep1(Request $request, Evaluation $evaluation)
    {
        $validated = $request->validate([
            'model_type' => 'required|string|in:App\Models\Formation,App\Models\Course,App\Models\Chapter',
        ]);

        // Mettre à jour la session
        $sessionData = session('evaluation_edit');
        $sessionData['new_model_type'] = $validated['model_type'];
        session(['evaluation_edit' => $sessionData]);

        return redirect()->route('evaluations.edit.step2', $evaluation);
    }

    // Étape 2 : Modifier l'élément associé
    public function editStep2(Evaluation $evaluation)
    {
        if (!session()->has('evaluation_edit.new_model_type')) {
            return redirect()->route('evaluations.edit.step1', $evaluation);
        }

        $modelType = session('evaluation_edit.new_model_type');
        $modelClass = $modelType;
        
        // Récupérer tous les éléments pour ce type avec gestion spécifique pour les chapitres
        if ($modelType === 'App\Models\Chapter') {
            // Les chapitres n'ont pas de colonne description, seulement content
            $items = $modelClass::select('id', 'title')->paginate(10);
            
            // Ajouter une description vide pour chaque chapitre pour éviter les erreurs dans la vue
            $items->getCollection()->transform(function ($item) {
                $item->description = ''; // Initialiser avec une description vide
                return $item;
            });
        } else {
            // Formations et cours ont une colonne description
            $items = $modelClass::select('id', 'title', 'description')->paginate(10);
        }
        
        // Récupérer l'élément actuellement associé à l'évaluation
        $currentModelId = $evaluation->evaluatable_id;
        
        return view('authenticated.owners.evaluations.edit-step-2', compact('evaluation', 'items', 'modelType', 'currentModelId'));
    }

    public function updateStep2(Request $request, Evaluation $evaluation)
    {
        $validated = $request->validate([
            'model_id' => 'required|integer',
        ]);

        // Mettre à jour la session
        $sessionData = session('evaluation_edit');
        $sessionData['new_model_id'] = $validated['model_id'];
        session(['evaluation_edit' => $sessionData]);

        return redirect()->route('evaluations.edit.step3', $evaluation);
    }

    // Étape 3 : Modifier les détails de l'évaluation
    public function editStep3(Evaluation $evaluation)
    {
        if (!session()->has('evaluation_edit.new_model_id')) {
            return redirect()->route('evaluations.edit.step1', $evaluation);
        }

        $sessionData = session('evaluation_edit');
        $modelClass = $sessionData['new_model_type'];
        $model = $modelClass::findOrFail($sessionData['new_model_id']);
        
        // Gérer spécifiquement les chapitres qui n'ont pas de description
        if ($sessionData['new_model_type'] === 'App\Models\Chapter') {
            // Ajouter une description vide si elle n'existe pas
            if (!isset($model->description)) {
                $model->description = ''; // Description vide pour les chapitres
            }
        }
        
        return view('authenticated.owners.evaluations.edit-step-3', compact('evaluation', 'model', 'sessionData'));
    }

    public function updateStep3(Request $request, Evaluation $evaluation)
    {
        $sessionData = session('evaluation_edit');
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scoring_mode' => 'nullable|string|in:pourcentage,points',
            'passing_score' => 'nullable|integer|min:0|max:100',
            'duration' => 'nullable|integer|min:1',
            'total_questions' => 'nullable|integer|min:1',
            'max_attempts' => 'nullable|integer|min:1|max:10',
            'importance' => 'required|string|in:mandatory,optional',
        ]);


        // Mettre à jour l'évaluation
        $evaluation->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'scoring_mode' => $validated['scoring_mode'] ?? 'pourcentage',
            'passing_score' => $validated['passing_score'] ?? 60,
            'duration' => $validated['duration'],
            'total_questions' => $validated['total_questions'] ?? $evaluation->questions->count(),
            'max_attempts' => $validated['max_attempts'] ?? NULL,
            'can_reset' => $validated['can_reset'] ?? true,
            'importance' => $validated['importance'],
        ]);

        // Récupérer le nouveau modèle associé
        $newModelClass = $sessionData['new_model_type'];
        $newModel = $newModelClass::findOrFail($sessionData['new_model_id']);

        // Vérifier si une évaluation existe déjà pour ce nouveau modèle (sauf pour l'évaluation actuelle)
        $existingEvaluation = Evaluation::where('evaluatable_type', $newModelClass)
            ->where('evaluatable_id', $sessionData['new_model_id'])
            ->where('id', '!=', $evaluation->id)
            ->first();

        if ($existingEvaluation) {
            return redirect()->back()->withErrors(['title' => 'Une évaluation existe déjà pour ce modèle.']);
        }

        // Mettre à jour l'association
        $evaluation->evaluatable()->associate($newModel);
        $evaluation->save();

        // Nettoyer la session
        session()->forget('evaluation_edit');

        return redirect()->route('evaluations.show', $evaluation->id)
            ->with('success', 'Évaluation mise à jour avec succès !');
    }

    public function destroy(Evaluation $evaluation)
    {
        $evaluation->delete();

        return redirect()->route('evaluations.index')->with('success', 'Évaluation supprimée !');
    }
}
