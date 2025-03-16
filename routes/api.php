<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\GalerieController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\UsersController;

// Routes publiques pour l'inscription et la connexion
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/posts', [PostsController::class, 'indexPosts']);
Route::get('/categories', [CategoriesController::class, 'indexCategories']);
Route::get('/posts/{id}', [PostsController::class, 'showPost']);
Route::get('/galerie', [GalerieController::class, 'index']);
Route::get('/random-post', [PostsController::class, 'getRandomPost']);
Route::get('/last-three-posts', [PostsController::class, 'getLastThreePosts']);


// Groupe de routes protégées par le middleware Sanctum
Route::middleware('auth:sanctum')->group(function () {
    // Route pour récupérer l'utilisateur authentifié
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/user/{id}', [UsersController::class, 'findUserById']);
    Route::put('/edit/user/{id}', [UsersController::class, 'updateUser']);
    Route::put('/user/{id}/edit/password', [UsersController::class, 'updatePassword']);

    Route::post('/comments/{postId}', [CommentsController::class, 'createComment']);
    
    // Route pour la déconnexion
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/dash/logout', [AuthController::class, 'dashLogout']);
});


// ------------------ DASHBOARD ------------------ //

Route::post('/dash/login', [AuthController::class, 'dashLogin']);
// Groupe de routes protégées par le middleware Sanctum et admin
Route::middleware(['auth:sanctum', 'admin'])->group(function () {

   // ------------- Routes des users
    Route::get('/dash/users', [UsersController::class, 'index']);
    Route::get('/dash/user/{id}', [UsersController::class, 'findUserById']);
    Route::post('/dash/user/{id}', [UsersController::class, 'updateUser']);
    Route::delete('/dash/user/{id}/delete', [UsersController::class, 'deleteUser']);

    // ------------- Routes des posts
    Route::get('/dash/posts', [PostsController::class, 'dashIndexPosts']);
    Route::get('/dash/post/{id}', [PostsController::class, 'showPost']);
    Route::delete('/dash/post/{id}/delete', [PostsController::class, 'deletePost']);
});
