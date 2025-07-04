<?php

namespace App\Http\Controllers;

use App\Models\Quizz;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class QuizController extends Controller
{
    public function listing(Request $request): Response
    {
        $subject = $request->query('subject');

        $query = Quizz::with('blocs');

        if ($subject) {
            $query->where('subject', $subject);
        }

        return Inertia::render('Quiz/Listing', [
            'quizzes' => $query->get(),
            'currentSubject' => $subject,
        ]);
    }

    public function show(Quizz $quizz): Response
    {
        return Inertia::render('Quiz/Show', [
            'quizz' => Quizz::with('blocs.questions')->find($quizz->id),
        ]);
    }
}
