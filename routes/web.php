<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\SpendController;
use App\Http\Controllers\IncomeController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Spending routes
    Route::get('/spend', [SpendController::class, 'index']);
    Route::post('/spend', [SpendController::class, 'store']);
    Route::put('/spend/{spend}', [SpendController::class, 'update']);
    Route::delete('/spend/{spend}', [SpendController::class, 'destroy']);

    // Income routes
    Route::get('/income', [IncomeController::class, 'index']);
    Route::post('/income', [IncomeController::class, 'store']);
    Route::put('/income/{income}', [IncomeController::class, 'update']);
    Route::delete('/income/{income}', [IncomeController::class, 'destroy']);
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
