<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SupportConversation;
use App\Models\SupportMessage;
use App\Events\SupportMessageSent;
use App\Events\SupportConversationUpdated;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewSupportTicketMail;

class SupportController extends Controller
{
    // --------------- PUBLIC ------------------------

    public function getUserConversation()
    {
        $user = Auth::user();

        $conversation = SupportConversation::with(['messages.sender'])
            ->where('user_id', $user->id)
            ->where('status', 'open')
            ->latest()
            ->first();

        return response()->json([
            'conversation' => $conversation
        ]);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string'
        ]);

        $user = Auth::user();

        $conversation = SupportConversation::where('user_id', $user->id)
            ->where('status', 'open')
            ->latest()
            ->first();

        $isNewTicket = false;

        if (!$conversation) {
            $conversation = SupportConversation::create([
                'user_id' => $user->id,
                'status' => 'open',
                'last_message_at' => now(),
            ]);

            $isNewTicket = true;
        }

        $message = SupportMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'sender_role' => 'user',
            'message' => $request->message,
            'is_read' => false,
        ]);

        $conversation->update([
            'last_message_at' => now()
        ]);

        $message->load('sender');
        $conversation->load('user');

        if ($isNewTicket) {
            $clientEmail = env('CONTACT_RECEIVER_EMAIL');

            if ($clientEmail) {
                Mail::to($clientEmail)->send(
                    new NewSupportTicketMail($conversation, $message)
                );
            }
        }

        broadcast(new SupportMessageSent($message))->toOthers();
        broadcast(new SupportConversationUpdated($conversation))->toOthers();

        return response()->json([
            'message' => $message
        ]);
    }

    public function markAsRead()
    {
        $user = Auth::user();

        $conversation = SupportConversation::where('user_id', $user->id)
            ->where('status', 'open')
            ->latest()
            ->first();

        if (!$conversation) {
            return response()->json([
                'message' => 'Aucune conversation.'
            ]);
        }

        SupportMessage::where('conversation_id', $conversation->id)
            ->where('sender_role', 'admin')
            ->where('is_read', false)
            ->update([
                'is_read' => true
            ]);

        return response()->json([
            'message' => 'Messages marqués comme lus.'
        ]);
    }

    // ---------------- DASHBOARD --------------------

    public function adminIndexOpen()
    {
        $user = Auth::user();

        if (!$user || !$user->isAdmin) {
            return response()->json([
                'message' => 'Non autorisé.'
            ], 403);
        }

        $conversations = SupportConversation::with('user')
            ->withCount([
                'messages as unread_count' => function ($query) {
                    $query->where('sender_role', 'user')
                        ->where('is_read', false);
                }
            ])
            ->where('status', 'open')
            ->orderByDesc('unread_count')
            ->orderByDesc('last_message_at')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'conversations' => $conversations
        ]);
    }

    public function adminIndexClosed()
    {
        $user = Auth::user();

        if (!$user || !$user->isAdmin) {
            return response()->json([
                'message' => 'Non autorisé.'
            ], 403);
        }

        $conversations = SupportConversation::with('user')
            ->where('status', 'closed')
            ->orderByDesc('last_message_at')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'conversations' => $conversations
        ]);
    }

    public function adminShow($id)
    {
        $user = Auth::user();

        if (!$user || !$user->isAdmin) {
            return response()->json([
                'message' => 'Non autorisé.'
            ], 403);
        }

        $conversation = SupportConversation::find($id);

        if (!$conversation) {
            return response()->json([
                'message' => 'Conversation introuvable.'
            ], 404);
        }

        SupportMessage::where('conversation_id', $conversation->id)
            ->where('sender_role', 'user')
            ->where('is_read', false)
            ->update([
                'is_read' => true
            ]);

        $conversation->load([
            'user',
            'messages.sender'
        ]);

        broadcast(new SupportConversationUpdated($conversation))->toOthers();

        return response()->json([
            'conversation' => $conversation
        ]);
    }

    public function adminSendMessage(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string'
        ]);

        $user = Auth::user();

        if (!$user || !$user->isAdmin) {
            return response()->json([
                'message' => 'Non autorisé.'
            ], 403);
        }

        $conversation = SupportConversation::find($id);

        if (!$conversation) {
            return response()->json([
                'message' => 'Conversation introuvable.'
            ], 404);
        }

        $message = SupportMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'sender_role' => 'admin',
            'message' => $request->message,
            'is_read' => false,
        ]);

        $conversation->update([
            'status' => 'open',
            'last_message_at' => now(),
        ]);

        $message->load('sender');
        $conversation->load('user');

        broadcast(new SupportMessageSent($message))->toOthers();
        broadcast(new SupportConversationUpdated($conversation))->toOthers();

        return response()->json([
            'message' => $message
        ]);
    }

    public function updateConversationStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:open,closed'
        ]);

        $user = Auth::user();

        if (!$user || !$user->isAdmin) {
            return response()->json([
                'message' => 'Non autorisé.'
            ], 403);
        }

        $conversation = SupportConversation::find($id);

        if (!$conversation) {
            return response()->json([
                'message' => 'Conversation introuvable.'
            ], 404);
        }

        $conversation->update([
            'status' => $request->status
        ]);

        $conversation->load('user');

        broadcast(new SupportConversationUpdated($conversation))->toOthers();

        return response()->json([
            'message' => 'Statut de la conversation mis à jour.',
            'conversation' => $conversation
        ]);
    }
}