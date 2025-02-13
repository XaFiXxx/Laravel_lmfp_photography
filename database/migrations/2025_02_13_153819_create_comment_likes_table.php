<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('comment_likes', function (Blueprint $table) {
            // Clé étrangère vers la table users (utilisateur qui like)
            $table->unsignedBigInteger('user_id');
            // Clé étrangère vers la table comments (commentaire liké)
            $table->unsignedBigInteger('comment_id');

            // Timestamp pour suivre la date du like
            $table->timestamps();

            // Définition des clés étrangères
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('comment_id')->references('id')->on('comments')->onDelete('cascade');

            // Clé primaire composée pour empêcher les doublons
            $table->primary(['user_id', 'comment_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comment_likes');
    }
};
