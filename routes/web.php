<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\SpendController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/spend', [SpendController::class, 'index']);
    Route::post('/spend', [SpendController::class, 'store']);
    Route::put('/spend/{spend}', [SpendController::class, 'update']);
    Route::delete('/spend/{spend}', [SpendController::class, 'destroy']);
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
