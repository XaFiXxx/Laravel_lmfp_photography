<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Routes publiques pour l'inscription et la connexion
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Groupe de routes protégées par le middleware Sanctum
Route::middleware('auth:sanctum')->group(function () {
    // Route pour récupérer l'utilisateur authentifié
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // Route pour la déconnexion (révocation du token)
    Route::post('/logout', [AuthController::class, 'logout']);
});
