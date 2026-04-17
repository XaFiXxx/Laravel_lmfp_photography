<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\GalerieController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\SupportController;

// Routes publiques pour l'inscription et la connexion
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/resend-verification-email', [AuthController::class, 'resendVerificationEmail'])->middleware('throttle:3,1');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Routes publiques NewsLetter
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe']);
Route::get('/newsletter/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe']);

// Routes pour les posts 
Route::get('/posts', [PostsController::class, 'indexPosts']);
Route::get('/posts/{slug}', [PostsController::class, 'showPost']);
Route::get('/random-post', [PostsController::class, 'getRandomPost']);
Route::get('/last-three-posts', [PostsController::class, 'getLastThreePosts']);
Route::get('/last-two-posts', [PostsController::class, 'getLastTwoPosts']);

// Routes pour les catégories
Route::get('/categories', [CategoriesController::class, 'indexCategories']);
Route::get('/category/{id}', [PostsController::class, 'getPostsByCategory']);
Route::get('/categories/{id}', [CategoriesController::class, 'showCategory']);

// Routes pour la galerie
Route::get('/galerie', [GalerieController::class, 'index']);


// Groupe de routes protégées par le middleware Sanctum
Route::middleware('auth:sanctum')->group(function () {
    // Route pour récupérer l'utilisateur authentifié
    Route::get('/user', [UsersController::class, 'me']);

    // Route pour les users
    Route::get('/user/{id}', [UsersController::class, 'findUserById']);
    Route::post('/edit/user/{id}', [UsersController::class, 'updateUser']);
    Route::put('/user/{id}/edit/password', [UsersController::class, 'updatePassword']);

    // Route pour les commentaires
    Route::post('/comments/{postId}', [CommentsController::class, 'createComment']);
    Route::put('/comment/{id}/edit', [CommentsController::class, 'updateComment']);
    Route::delete('/comment/{id}/delete', [CommentsController::class, 'deleteComment']);

    // Route pour le formulaire de contact 
    Route::post('/contact', [ContactController::class, 'send']);

    // Routes pour le chat 
    Route::get('/support/conversation', [SupportController::class, 'getUserConversation']);
    Route::post('/support/messages', [SupportController::class, 'sendMessage']);
    Route::post('/support/conversation/read', [SupportController::class, 'markAsRead']);
    
    // Route pour la déconnexion
    Route::post('/logout', [AuthController::class, 'logout']);
});


// ------------------ DASHBOARD ------------------ //

Route::post('/dash/login', [AuthController::class, 'dashLogin']);
// Groupe de routes protégées par le middleware Sanctum et admin
Route::middleware(['auth:sanctum', 'admin'])->group(function () {

   // ------------- Routes des stats

    Route::get('/dash/stats', [StatsController::class, 'stats']);

   // ------------- Routes des users
    Route::get('/dash/users', [UsersController::class, 'index']);
    Route::post('/dash/user/create', [UsersController::class, 'createUser']);
    Route::get('/dash/user/{id}', [UsersController::class, 'findUserById']);
    Route::post('/dash/user/{id}', [UsersController::class, 'updateUserDash']);
    Route::delete('/dash/user/{id}/delete', [UsersController::class, 'deleteUser']);

    // ------------- Routes des posts
    Route::get('/dash/posts', [PostsController::class, 'dashIndexPosts']);
    Route::get('/dash/post/{id}', [PostsController::class, 'showPost']);
    Route::delete('/dash/post/{id}/delete', [PostsController::class, 'deletePost']);
    Route::put('/dash/post/{id}/edit', [PostsController::class, 'updatePost']);
    Route::post('/dash/post/create', [PostsController::class, 'createPost']);

    // ------------- Routes des posts
    Route::get('/dash/comments', [CommentsController::class, 'indexComments']);
    Route::delete('/dash/comments/{id}', [CommentsController::class, 'deleteDashComment']);

    // ------------- Routes de la gallerie 
    Route::get('/dash/gallery', [GalerieController::class, 'indexDash']);
    Route::delete('/dash/gallery/{id}', [GalerieController::class, 'delete']);

    // ------------- Routes des catégories
    Route::get('/dash/categories', [CategoriesController::class, 'indexCategories']);
    Route::get('/dash/category/{id}', [CategoriesController::class, 'showCategory']);   
    Route::post('/dash/category/create', [CategoriesController::class, 'createCategory']);
    Route::delete('/dash/category/{id}/delete', [CategoriesController::class, 'deleteCategory']);
    Route::put('/dash/category/{id}/edit', [CategoriesController::class, 'updateCategory']);

    // ------------- Routes des catégories
    Route::get('/dash/newsletter', [NewsletterController::class, 'index']);
    Route::post('/dash/newsletter/send', [NewsletterController::class, 'send']);
    Route::post('/dash/newsletter/send-post', [NewsletterController::class, 'sendPost']);

    // ------------- Routes pour le chat 
    Route::get('/dashboard/support/conversations', [SupportController::class, 'adminIndexOpen']);
    Route::get('/dashboard/support/conversations/history', [SupportController::class, 'adminIndexClosed']);
    Route::get('/dashboard/support/conversations/{id}', [SupportController::class, 'adminShow']);
    Route::post('/dashboard/support/conversations/{id}/messages', [SupportController::class, 'adminSendMessage']);
    Route::patch('/dashboard/support/conversations/{id}/status', [SupportController::class, 'updateConversationStatus']);


    Route::post('/dash/logout', [AuthController::class, 'dashLogout']);
});
