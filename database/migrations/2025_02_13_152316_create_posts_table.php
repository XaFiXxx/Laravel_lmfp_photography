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
        Schema::create('posts', function (Blueprint $table) {
            // ID auto-incrémenté
            $table->bigIncrements('id');

            // Colonnes pour le post
            $table->string('title', 255);
            $table->string('image', 255)->nullable();
            $table->text('description')->nullable();

            // Clé étrangère vers l’utilisateur (assure-toi que la table 'users' existe déjà)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Timestamps (created_at, updated_at)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
