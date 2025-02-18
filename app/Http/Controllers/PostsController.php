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
}
