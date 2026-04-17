<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Models\Galerie;
use App\Models\Comment;
use App\Models\Categorie;
use App\Models\Subscriber;

class StatsController extends Controller
{
    public function stats()
    {
        $latestPosts = Post::with('categories')
            ->latest()
            ->take(3)
            ->get();

        $latestUsers = User::latest()
            ->take(4)
            ->get();

        $latestComments = Comment::with(['user', 'post'])
            ->latest()
            ->take(5)
            ->get();

        $latestSubscribers = Subscriber::orderByDesc('subscribed_at')
            ->take(5)
            ->get();

        return response()->json([
            'posts' => Post::count(),
            'gallery' => Galerie::count(),
            'comments' => Comment::count(),
            'categories' => Categorie::count(),
            'users' => User::count(),

            'newsletter_active' => Subscriber::where('is_active', true)->count(),
            'newsletter_inactive' => Subscriber::where('is_active', false)->count(),

            'posts_without_categories' => Post::doesntHave('categories')->count(),
            'posts_without_image' => Post::whereNull('image')
                ->orWhere('image', '')
                ->count(),

            'latest_posts' => $latestPosts,
            'latest_users' => $latestUsers,
            'latest_comments' => $latestComments,
            'latest_newsletter_subscribers' => $latestSubscribers,
        ]);
    }
}