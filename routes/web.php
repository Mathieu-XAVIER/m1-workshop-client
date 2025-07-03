<?php

use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\HomeController;
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

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/quiz', [QuizController::class, 'listing'])->name('quiz.listing');
Route::get('/quiz/{quizz}', [QuizController::class, 'show'])->name('quiz.show');

Route::get('/login', function () {
    return Inertia::render('Login');
})->name('login');

Route::post('/api/register', [RegistrationController::class, 'store'])->name('register');

Route::post('/api/login', function (Request $request) {
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

Route::get('/forgot-password', function () {
    return Inertia::render('ForgotPassword');
})->name('password.request');

Route::post('/forgot-password', function (Request $request) {
    $request->validate(['email' => 'required|email']);

    $status = Password::sendResetLink(
        $request->only('email')
    );

    return back()->with('status', __($status));
})->name('password.email');

Route::get('/reset-password/{token}', function (Request $request, $token) {
    return Inertia::render('ResetPassword', [
        'token' => $token,
        'email' => $request->email,
    ]);
})->name('password.reset');

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
