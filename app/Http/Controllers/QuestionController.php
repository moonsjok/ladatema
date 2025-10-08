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
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $question->update($validated);

        return redirect()->route('authenticated.owners.questions.index')->with('success', 'Évaluation mise à jour !');
    }

    public function destroy(Question $question)
    {
        $question->delete();

        return redirect()->route('authenticated.owners.questions.index')->with('success', 'Évaluation supprimée !');
    }
}
