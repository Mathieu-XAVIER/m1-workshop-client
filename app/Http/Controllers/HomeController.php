<?php

namespace App\Http\Controllers;

use App\Models\Quizz;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Home', [
            'quizzes' => Quizz::orderBy('created_at', 'desc')
                ->get(),
        ]);
    }
}
