<?php

namespace App\Events;

use App\Models\SupportConversation;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SupportConversationUpdated implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public $conversation;

    public function __construct(SupportConversation $conversation)
    {
        $conversation->load('user');

        $unreadCount = $conversation->messages()
            ->where('sender_role', 'user')
            ->where('is_read', false)
            ->count();

        $this->conversation = [
            'id' => $conversation->id,
            'status' => $conversation->status,
            'last_message_at' => $conversation->last_message_at,
            'unread_count' => $unreadCount,
            'user' => [
                'id' => $conversation->user?->id,
                'firstname' => $conversation->user?->firstname,
                'lastname' => $conversation->user?->lastname,
                'email' => $conversation->user?->email,
            ],
        ];
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('admin.support'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'support.conversation.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'conversation' => $this->conversation,
        ];
    }
}