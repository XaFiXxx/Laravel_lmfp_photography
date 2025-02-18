<?php

namespace App\Http\Controllers;
use App\Models\Comment;

use Illuminate\Http\Request;

class CommentsController extends Controller
{
    public function createComment(Request $request, $postId)
    {
        $validatedData = $request->validate([
            'content' => 'required|string',
        ]);
    
        // Assurez-vous que l'utilisateur est authentifié
        $userId = auth()->id(); 
    
        $comment = Comment::create([
            'post_id' => $postId,
            'content' => $validatedData['content'],
            'user_id' => $userId, // On associe le commentaire à l'utilisateur connecté
        ]);
    
        $comment->load('user');
        
        return response()->json($comment, 201);
    }
}
