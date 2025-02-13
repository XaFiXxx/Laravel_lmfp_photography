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
        Schema::create('galleries', function (Blueprint $table) {
            $table->id();
            
            // Stocke l'URL ou le chemin de l'image
            $table->string('picture', 255);
            
            // Clé étrangère vers la table posts
            $table->unsignedBigInteger('post_id');
            
            // Timestamps pour garder une trace des ajouts/modifications
            $table->timestamps();

            // Relation avec la table posts
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('galleries');
    }
};
