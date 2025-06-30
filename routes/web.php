<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Page d'accueil avec les catégories
Route::get('/', function () {
    return Inertia::render('Home');
});
