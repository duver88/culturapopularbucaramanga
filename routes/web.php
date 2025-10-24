<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SurveyController as AdminSurveyController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SurveyController;
use Illuminate\Support\Facades\Route;

// Ruta principal - redirige al login
Route::get('/', function () {
    return redirect('/survey/encuesta-de-favorabilidad-alcaldia-de-bucaramanga-BU3aPT');
});

// Rutas de autenticación
Route::get('/HZlflogiis', [AuthController::class, 'showLogin'])->name('login');
Route::post('/HZlflogiis', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas públicas de encuestas
Route::get('/survey/{slug}', [SurveyController::class, 'show'])->name('surveys.show');
Route::post('/survey/{slug}/vote', [SurveyController::class, 'vote'])
    ->middleware('prevent.duplicate.vote')
    ->name('surveys.vote');
Route::get('/survey/{slug}/thanks', [SurveyController::class, 'thanks'])->name('surveys.thanks');
Route::get('/survey/{slug}/check-vote', [SurveyController::class, 'checkVote'])->name('surveys.check-vote');

// Rutas del administrador (protegidas)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Gestión de encuestas
    Route::resource('surveys', AdminSurveyController::class);
    Route::post('/surveys/{survey}/publish', [AdminSurveyController::class, 'publish'])->name('surveys.publish');
    Route::post('/surveys/{survey}/unpublish', [AdminSurveyController::class, 'unpublish'])->name('surveys.unpublish');
});

