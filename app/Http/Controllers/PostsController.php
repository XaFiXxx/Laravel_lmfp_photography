<?php

namespace App\Http\Controllers;

use App\Models\Post; // Importation du modèle avec le nom correct (singulier)
use Illuminate\Http\Request;

class PostsController extends Controller
{
    // Affiche la liste des posts
    public function indexPosts()
    {
        $posts = Post::all();
        return response()->json($posts);
    }

    public function showPost($id)
    {
        $post = Post::with([
            'galery',
            'comments' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'comments.user'
        ])->findOrFail($id);
        return response()->json($post);
    }

    public function getRandomPost()
    {
        // Récupère un post au hasard avec ses relations (galery et commentaires avec utilisateur)
        $post = Post::with('galery', 'comments.user')->inRandomOrder()->first();
        return response()->json($post);
    }

    public function getLastThreePosts()
    {
        // Récupère les 3 derniers posts publiés triés par date de création décroissante
        $posts = Post::with('galery', 'comments.user')
                    ->orderBy('created_at', 'desc')
                    ->limit(2)
                    ->get();
        return response()->json($posts);
    }

    public function dashIndexPosts()
    {
        // Récupère les posts avec leurs relations (galery et commentaires avec utilisateur)
        $posts = Post::with('galery', 'comments.user', 'user')->get();
        return response()->json($posts);
    }

}
