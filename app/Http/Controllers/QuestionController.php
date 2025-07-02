<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class QuestionController extends Controller
{
    /**
     * Page d'accueil des questions
     */
    public function index(): Response
    {
        return Inertia::render('Questions/Index', [
            'questions' => Question::select(['id', 'title', 'type', 'level', 'status', 'created_at'])
                ->where('status', 'approved')
                ->orderBy('created_at', 'desc')
                ->get()
        ]);
    }

    /**
     * Page pour afficher une question (quiz)
     */
    public function show($id): Response
    {
        $question = Question::where('status', 'approved')
        ->findOrFail($id);

        return Inertia::render('Questions/Show', [
            'question' => Question::where('status', 'approved')->findOrFail($id),
            'quizResult' => session('quiz_result'),
        ]);
    }

    /**
     * Vérifier la réponse d'un utilisateur (pour le quiz)
     */
    public function checkAnswer(Request $request, $id)
    {
        $question = Question::where('status', 'approved')
            ->findOrFail($id);

        $request->validate([
            'user_answer' => 'required',
            'timer' => 'nullable|integer|min:0',
        ]);

        $isCorrect = $this->compareAnswers(
            $request->user_answer,
            $question->answer,
            $question->type->value
        );

        return back()->with([
            'quiz_result' => [
                'is_correct' => $isCorrect,
                'user_answer' => $request->user_answer,
                'correct_answer' => $question->answer,
                'explanation' => $question->answer['explanation'] ?? null,
                'timer' => $request->timer,
            ]
        ]);
    }

    /**
     * Comparer les réponses selon le type de question
     */
    private function compareAnswers($userAnswer, $correctAnswer, $questionType): bool
    {
        switch ($questionType) {
            case 'multiple_choice':
                if (!isset($correctAnswer['correct'])) {
                    return false;
                }
                return (int)$userAnswer === (int)$correctAnswer['correct'];

            case 'true_false':
                return isset($correctAnswer['correct']) &&
                    (bool)$userAnswer === (bool)$correctAnswer['correct'];

            case 'short_answer':
            case 'fill_in_the_blank':
                $userText = strtolower(trim($userAnswer));
                $correctText = strtolower(trim($correctAnswer['correct'] ?? ''));
                return $userText === $correctText;

            case 'long_answer':
                return true;

            default:
                return false;
        }
    }
}
