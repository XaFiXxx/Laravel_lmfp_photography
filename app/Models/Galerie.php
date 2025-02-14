<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Galerie extends Model
{
    protected $table = 'galleries'; // Nom de la table
    protected $fillable = ['picture', 'description', 'post_id']; // Champs modifiables

    public function post()
    {
        return $this->belongsTo('App\Models\Post');
    }
}
