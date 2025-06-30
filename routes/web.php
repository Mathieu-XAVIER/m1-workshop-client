<?php

use App\Http\Controllers\Auth\RegistrationController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
});

Route::post('/api/register', [RegistrationController::class, 'store'])->name('register');

Route::get('/home', function() {
    return Inertia::render('Home');
})->name('home');
