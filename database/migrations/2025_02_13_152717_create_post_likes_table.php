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
        Schema::create('post_likes', function (Blueprint $table) {
            $table->id();
            
            // Clé étrangère vers la table users
            $table->unsignedBigInteger('user_id');
            
            // Clé étrangère vers la table posts
            $table->unsignedBigInteger('post_id');

            // Timestamps pour savoir quand le like a été fait
            $table->timestamps();

            // Définition des clés étrangères
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');

            // Empêcher un utilisateur de liker plusieurs fois le même post
            $table->unique(['user_id', 'post_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_likes');
    }
};
