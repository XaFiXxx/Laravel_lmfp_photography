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
        Schema::create('posts_has_categories', function (Blueprint $table) {
            // On ne crée pas de colonne 'id' auto-incrémentée, 
            // car on veut une clé primaire composée
            
            // Clé étrangère vers la table posts
            $table->unsignedBigInteger('post_id');
            // Clé étrangère vers la table categories
            $table->unsignedBigInteger('category_id');
            
            // Timestamps (created_at, updated_at)
            $table->timestamps();

            // Définition des clés étrangères
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');

            // Clé primaire composée
            $table->primary(['post_id', 'category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts_has_categories');
    }
};
