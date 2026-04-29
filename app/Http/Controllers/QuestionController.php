<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Evaluation;
use App\Models\Question;


class QuestionController extends Controller
{
    public function index()
    {
        $questions = Question::all();
        return view('authenticated.owners.questions.index', compact('questions'));
    }

    public function create(Request $request)
    {
        $evaluatableType = $request->query('evaluatable_type');
        $evaluatableId = $request->query('evaluatable_id');

        $evaluation = Evaluation::where([
            'evaluatable_type' => $evaluatableType,
            'evaluatable_id' => $evaluatableId,
        ])->firstOrFail();

        return view('authenticated.owners.questions.create', compact('evaluation'));
    }

    public function store(Request $request, Evaluation $evaluation)
    {
        $validated = $request->validate([
            'type' => 'required|string|in:single_choice,multiple_choice',
            'question_text' => 'required|string|max:255',
            'answers' => 'required|array',
            'answers.*.text' => 'required|string|max:255',
            'answers.*.is_correct' => 'nullable|boolean',
        ]);

        $question = $evaluation->questions()->create([
            'type' => $validated['type'],
            'question_text' => $validated['question_text'],
        ]);

        foreach ($validated['answers'] as $answer) {
            $question->answers()->create([
                'answer_text' => $answer['text'],
                'is_correct' => $answer['is_correct'] ?? false,
            ]);
        }

        return redirect()->route('questions.create', [
            'evaluatable_type' => $evaluation->evaluatable_type,
            'evaluatable_id' => $evaluation->evaluatable_id,
        ])->with('success', 'Question ajoutée avec succès !');
    }

    public function show(Question $question)
    {
        return view('authenticated.owners.questions.show', compact('question'));
    }

    public function edit(Question $question)
    {
        return view('authenticated.owners.questions.edit', compact('question'));
    }

    public function update(Request $request, Question $question)
    {
        $validated = $request->validate([
            'question_text' => 'required|string|max:255',
            'type' => 'required|string|in:single_choice,multiple_choice,text,find_intruder',
        ]);

        $oldType = $question->type;
        $newType = $validated['type'];

        // Mettre à jour la question
        $question->update([
            'question_text' => $validated['question_text'],
            'type' => $newType,
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

        return redirect()->route('evaluations.show', $question->evaluation_id)
            ->with('success', 'Question mise à jour avec succès !');
    }

    public function destroy(Question $question)
    {
        $question->delete();

        return redirect()->route('authenticated.owners.questions.index')->with('success', 'Évaluation supprimée !');
    }
}
