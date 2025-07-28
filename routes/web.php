<?php

use App\Http\Controllers\Trivia\QuestionController;
use App\Http\Controllers\Trivia\TriviaController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [TriviaController::class, 'index'])->name('home');

Route::get('question', [QuestionController::class, 'getTrivia'])->name('question');
Route::post('answer', [QuestionController::class, 'postAnswer'])->name('answer');
Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
