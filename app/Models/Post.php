<?php

namespace App\Models;
use App\Models\User;
use App\Models\Comment;
use App\Models\Galerie;
use App\Models\categories;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'posts';
    protected $fillable = ['title', 'content', 'description', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function categories()
    {
        return $this->hasMany(categories::class);
    }

    public function galery()
    {
        return $this->hasMany(Galerie::class, 'post_id', 'id');
    }

}
