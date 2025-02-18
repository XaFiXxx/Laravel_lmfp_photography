<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\GalerieController;
use App\Http\Controllers\CommentsController;

// Routes publiques pour l'inscription et la connexion
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/posts', [PostsController::class, 'indexPosts']);
Route::get('/categories', [CategoriesController::class, 'indexCategories']);
Route::get('/posts/{id}', [PostsController::class, 'showPost']);
Route::get('/galerie', [GalerieController::class, 'index']);

// Groupe de routes protégées par le middleware Sanctum
Route::middleware('auth:sanctum')->group(function () {
    // Route pour récupérer l'utilisateur authentifié
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/comments/{postId}', [CommentsController::class, 'createComment']);
    
    // Route pour la déconnexion
    Route::post('/logout', [AuthController::class, 'logout']);
});
