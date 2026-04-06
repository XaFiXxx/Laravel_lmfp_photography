<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Galerie;
use App\Models\Comment;
use App\Models\Categorie;

class StatsController extends Controller
{
    public function stats()
    {
        return response()->json([
            'posts' => Post::count(),
            'gallery' => Galerie::count(),
            'comments' => Comment::count(),
            'categories' => Categorie::count(),
        ]);
    }
}
