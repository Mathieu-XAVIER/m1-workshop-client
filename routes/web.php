<?php

use App\Http\Controllers\Auth\RegistrationController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\QuizController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('welcome');

Route::get('/login', function () {
    return Inertia::render('Login');
})->name('login');

Route::post('/api/register', [RegistrationController::class, 'store'])->name('register');

Route::post('/api/login', function(Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('/home');
    }

    throw ValidationException::withMessages([
        'email' => 'Les identifiants fournis sont incorrects.',
    ]);
})->name('login.store');

Route::get('/home', function() {
    return Inertia::render('Home');
})->name('home');

// ===============================================
// 🎯 Routes du système Quiz avec contrôleur
// ===============================================

// Routes principales des quiz
Route::get('/quiz', [QuizController::class, 'index'])->name('quiz.index');
Route::get('/quiz/{category}', [QuizController::class, 'show'])->name('quiz.category');

// ===============================================
// 🎯 API Routes pour le système Quiz
// ===============================================

Route::prefix('api/quiz')->group(function() {
    // Gestion de la progression
    Route::post('/{category}/progress', [QuizController::class, 'saveProgress'])->name('quiz.progress.save');
    Route::get('/{category}/progress', [QuizController::class, 'getProgress'])->name('quiz.progress.get');

    // Gestion des résultats
    Route::post('/{category}/results', [QuizController::class, 'submitResults'])->name('quiz.results.submit');

    // Actions sur les quiz
    Route::post('/{category}/restart', [QuizController::class, 'restart'])->name('quiz.restart');

    // Statistiques
    Route::get('/stats', [QuizController::class, 'getStats'])->name('quiz.stats.global');
    Route::get('/{category}/stats', [QuizController::class, 'getStats'])->name('quiz.stats.category');
});
