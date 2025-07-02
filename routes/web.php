<?php

use App\Http\Controllers\Auth\RegistrationController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\QuizController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

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

// Affiche le formulaire de demande de reset
Route::get('/forgot-password', function () {
    return Inertia::render('ForgotPassword');
})->name('password.request');

// Traite la demande de reset (envoie le mail)
Route::post('/forgot-password', function (Request $request) {
    $request->validate(['email' => 'required|email']);

    $status = Password::sendResetLink(
        $request->only('email')
    );

    return back()->with('status', __($status));
})->name('password.email');

// Affiche le formulaire de nouveau mot de passe (depuis le lien du mail)
Route::get('/reset-password/{token}', function (Request $request, $token) {
    return Inertia::render('ResetPassword', [
        'token' => $token,
        'email' => $request->email,
    ]);
})->name('password.reset');

// Traite la soumission du nouveau mot de passe
Route::post('/reset-password', function (Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ])->setRememberToken(Str::random(60));

            $user->save();

            event(new PasswordReset($user));
        }
    );

    return $status === Password::PASSWORD_RESET
        ? redirect()->route('login')->with('status', __($status))
        : back()->withErrors(['email' => [__($status)]]);
})->name('password.update');



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
