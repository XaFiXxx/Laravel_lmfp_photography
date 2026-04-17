<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_messages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('conversation_id')
                ->constrained('support_conversations')
                ->onDelete('cascade');

            $table->foreignId('sender_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->enum('sender_role', ['user', 'admin']);

            $table->text('message');

            $table->boolean('is_read')
                ->default(false);

            $table->timestamps();

            $table->index(['conversation_id', 'created_at']);
            $table->index(['sender_id', 'sender_role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_messages');
    }
};