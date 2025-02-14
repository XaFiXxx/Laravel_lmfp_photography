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

    // Affiche un post
    public function showPost($id)
    {
        $post = Post::with('galery', 'comments.user')->findOrFail($id);
        return response()->json($post);
    }
}
