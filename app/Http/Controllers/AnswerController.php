<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use Illuminate\Http\Request;

class AnswerController extends Controller
{
    /**
     * Affiche la liste des réponses.
     */
    public function index()
    {
        $answers = Answer::with('question')->get(); // Charge aussi la question associée
        return view('authenticated.owners.answers.index', compact('answers'));
    }

    /**
     * Affiche le formulaire pour créer une nouvelle réponse.
     */
    public function create()
    {
        return view('authenticated.owners.answers.create');
    }

    /**
     * Enregistre une nouvelle réponse.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'question_id' => 'required|exists:questions,id',
            'answer_text' => 'required|string|max:255',
            'is_correct' => 'required|boolean',
        ]);

        Answer::create($validated);

        return redirect()->route('answers.index')->with('success', 'Réponse ajoutée avec succès !');
    }

    /**
     * Affiche les détails d'une réponse spécifique.
     */
    public function show(Answer $answer)
    {
        return view('authenticated.owners.answers.show', compact('answer'));
    }

    /**
     * Affiche le formulaire pour modifier une réponse.
     */
    public function edit(Answer $answer)
    {
        return view('authenticated.owners.answers.edit', compact('answer'));
    }

    /**
     * Met à jour une réponse existante.
     */
    public function update(Request $request, Answer $answer)
    {
        // Récupère uniquement les champs présents dans la requête
        $data = $request->only(['question_id', 'answer_text', 'is_correct']);

        // Valide uniquement les champs présents dans la requête
        $validated = $request->validate([
            'question_id' => 'sometimes|required|string|max:255',
            'answer_text' => 'sometimes|required|string|max:255',
            'is_correct' => 'sometimes|nullable|boolean',
        ]);

        // Filtre les champs pour ne mettre à jour que ceux qui ont changé
        $updatedData = array_filter($validated, function ($value, $key) use ($answer) {
            return $answer->{$key} !== $value;
        }, ARRAY_FILTER_USE_BOTH);

        // Si aucun champ n'a changé, rediriger sans mise à jour
        if (empty($updatedData)) {
            return redirect()->route('answers.index')->with('info', 'Aucune modification détectée.');
        }

        // Mise à jour des champs modifiés
        $answer->update($updatedData);

        return redirect()->route('answers.index')->with('success', 'Réponse mise à jour avec succès !');
    }


    /**
     * Supprime une réponse.
     */
    public function destroy(Answer $answer)
    {
        $answer->delete();

        return redirect()->route('answers.index')->with('success', 'Réponse supprimée avec succès !');
    }
}
