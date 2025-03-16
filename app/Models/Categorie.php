<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categorie extends Model
{
    protected $table = 'categories';
    protected $fillable = ['name'];

    public function posts() {
        return $this->belongsToMany(Post::class, 'posts_has_categories', 'category_id', 'post_id');
    }
}
