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
        Schema::create('comments', function (Blueprint $table) {
            $table->id(); // Identifiant unique du commentaire
            
            // Clé étrangère vers la table users (auteur du commentaire)
            $table->unsignedBigInteger('user_id');
            // Clé étrangère vers la table posts (post commenté)
            $table->unsignedBigInteger('post_id');

            // Contenu du commentaire
            $table->text('content');

            // Timestamps pour enregistrer la date de création/modification
            $table->timestamps();

            // Définition des clés étrangères
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
