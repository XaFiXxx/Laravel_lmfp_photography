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

        $userId = auth()->id();

        $comment = Comment::create([
            'post_id' => $postId,
            'content' => $validatedData['content'],
            'user_id' => $userId,
        ]);

        $comment->load('user');

        return response()->json($comment, 201);
    }

    public function updateComment(Request $request, $id)
    {
        $validatedData = $request->validate([
            'content' => 'required|string',
        ]);

        $comment = Comment::findOrFail($id);

        // Vérifie que l'utilisateur connecté est bien l'auteur du commentaire
        if (auth()->id() !== $comment->user_id) {
            return response()->json([
                'message' => 'Action non autorisée.',
            ], 403);
        }

        $comment->content = $validatedData['content'];
        $comment->save();

        $comment->load('user');

        return response()->json([
            'message' => 'Commentaire modifié avec succès.',
            'comment' => $comment,
        ], 200);
    }

    public function deleteComment($id)
    {
        $comment = Comment::findOrFail($id);

        // Vérifie que l'utilisateur connecté est bien l'auteur du commentaire
        if (auth()->id() !== $comment->user_id) {
            return response()->json([
                'message' => 'Action non autorisée.',
            ], 403);
        }

        $comment->delete();

        return response()->json([
            'message' => 'Commentaire supprimé avec succès.',
        ], 200);
    }
}