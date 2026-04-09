<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GeneratePostSlugs extends Command
{
    protected $signature = 'posts:generate-slugs';
    protected $description = 'Generate unique slugs for existing posts';

    public function handle()
    {
        $posts = Post::all();

        foreach ($posts as $post) {
            if ($post->slug) {
                continue;
            }

            $baseSlug = Str::slug($post->title);
            $slug = $baseSlug;
            $counter = 1;

            while (
                Post::where('slug', $slug)
                    ->where('id', '!=', $post->id)
                    ->exists()
            ) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            $post->slug = $slug;
            $post->save();

            $this->info("Slug generated for post #{$post->id}: {$slug}");
        }

        $this->info('All missing slugs have been generated.');

        return Command::SUCCESS;
    }
}