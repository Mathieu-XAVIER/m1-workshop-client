<?php

use App\Http\Controllers\Auth\RegistrationController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('welcome');

Route::get('/login', function () {
    return Inertia::render('Login');
})->name('login');

Route::post('/api/register', [RegistrationController::class, 'store'])->name('register');
Route::post('/api/login', function() {
    // Logique de connexion à implémenter par l'équipe back-end
})->name('login.store');

Route::get('/home', function() {
    return Inertia::render('Home');
})->name('home');
