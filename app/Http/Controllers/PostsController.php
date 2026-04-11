<?php

namespace App\Http\Controllers;

use App\Models\Post; // Importation du modèle avec le nom correct (singulier)
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use App\Models\Comment;
use App\Models\Galerie;
use App\Models\User;
use App\Models\Categorie;

class PostsController extends Controller
{
    public function indexPosts(Request $request)
    {
        $query = Post::with('categories')->latest();

        if ($request->filled('category_id')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        $posts = $query->paginate(6);

        return response()->json($posts);
    }

    public function showPost($slug)
    {
        $post = Post::with([
            'galery',
            'categories',
            'comments' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'comments.user'
        ])->where('slug', $slug)->firstOrFail();

        return response()->json($post);
    }

    public function getRandomPost()
    {
        // Récupère un post au hasard avec ses relations (galery et commentaires avec utilisateur)
        $post = Post::with('galery', 'comments.user')->inRandomOrder()->first();
        return response()->json($post);
    }

    public function getLastThreePosts()
    {
        // Récupère les 3 derniers posts publiés triés par date de création décroissante
        $posts = Post::with('galery', 'comments.user')
                    ->orderBy('created_at', 'desc')
                    ->limit(3)
                    ->get();
        return response()->json($posts);
    }

    public function getLastTwoPosts()
    {
        // Récupère les 3 derniers posts publiés triés par date de création décroissante
        $posts = Post::with('galery', 'comments.user')
                    ->orderBy('created_at', 'desc')
                    ->limit(2)
                    ->get();
        return response()->json($posts);
    }

    public function getPostsByCategory($id)
    {
        $posts = Post::with('galery', 'comments.user')
                    ->whereHas('categories', function($query) use ($id) {
                        $query->where('id', $id);
                    })
                    ->get();

        return response()->json($posts);
    }

    // ------------------ DASHBOARD ------------------ //

    public function dashIndexPosts(Request $request)
    {
        $search = $request->query('search');

        $posts = Post::with([
                'galery',
                'categories',
                'comments.user',
                'user'
            ])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(12);

        return response()->json($posts);
    }


    public function deletePost($id)
    {
        try {
            // Récupère le post ou 404 s'il n'existe pas
            $post = Post::findOrFail($id);

            // =========================
            // SUPPRESSION IMAGE PRINCIPALE
            // =========================
            if ($post->image) {
                $imagePath = public_path($post->image);

                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            // =========================
            // SUPPRESSION DES IMAGES DE GALERIE
            // =========================
            foreach ($post->galery as $image) {
                $filePath = public_path($image->picture);

                if (file_exists($filePath)) {
                    unlink($filePath);
                }

                $image->delete();
            }

            // =========================
            // SUPPRESSION DES COMMENTAIRES
            // =========================
            foreach ($post->comments as $comment) {
                $comment->delete();
            }

            // =========================
            // DÉTACHER LES CATÉGORIES
            // =========================
            $post->categories()->detach();

            // =========================
            // SUPPRIMER LE POST
            // =========================
            $post->delete();

            return response()->json([
                'message' => 'Post bien supprimé'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la suppression du post',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updatePost(Request $request, $slug)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
            'gallery.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
            'categories' => 'nullable|string',
            'removed_gallery' => 'nullable|array',
            'removed_gallery.*' => 'integer',
        ]);

        if (!Auth::check()) {
            return response()->json([
                'message' => 'Utilisateur non authentifié.',
            ], 401);
        }

        DB::beginTransaction();

        try {
            $post = Post::where('slug', $slug)->firstOrFail();

            // Génération du slug unique
            $baseSlug = Str::slug($request->title);
            $newSlug = $baseSlug;
            $counter = 1;

            while (
                Post::where('slug', $newSlug)
                    ->where('id', '!=', $post->id)
                    ->exists()
            ) {
                $newSlug = $baseSlug . '-' . $counter;
                $counter++;
            }

            // Mise à jour texte
            $post->title = $request->title;
            $post->slug = $newSlug;
            $post->description = $request->description;

            // Image principale
            if ($request->hasFile('image')) {
                if ($post->image) {
                    $oldImagePath = public_path($post->image);

                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                $image = $request->file('image');
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

                $destinationPath = public_path('storage/img/posts/img');

                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }

                $image->move($destinationPath, $imageName);

                $post->image = 'storage/img/posts/img/' . $imageName;
            }

            $post->save();

            // Catégories
            if ($request->filled('categories')) {
                $categories = json_decode($request->categories, true);

                if (is_array($categories)) {
                    $post->categories()->sync($categories);
                }
            } else {
                $post->categories()->sync([]);
            }

            // Suppression images galerie
            if ($request->has('removed_gallery')) {
                $removedIds = $request->input('removed_gallery', []);

                if (is_array($removedIds) && !empty($removedIds)) {
                    $imagesToDelete = Galerie::where('post_id', $post->id)
                        ->whereIn('id', $removedIds)
                        ->get();

                    foreach ($imagesToDelete as $image) {
                        $filePath = public_path($image->picture);

                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }

                        $image->delete();
                    }
                }
            }

            // Ajout nouvelles images galerie
            if ($request->hasFile('gallery')) {
                $galleryDestinationPath = public_path('storage/img/posts/gallery');

                if (!file_exists($galleryDestinationPath)) {
                    mkdir($galleryDestinationPath, 0755, true);
                }

                foreach ($request->file('gallery') as $galleryImage) {
                    $galleryName = time() . '_' . uniqid() . '.' . $galleryImage->getClientOriginalExtension();

                    $galleryImage->move($galleryDestinationPath, $galleryName);

                    Galerie::create([
                        'post_id' => $post->id,
                        'picture' => 'storage/img/posts/gallery/' . $galleryName,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Post mis à jour avec succès.',
                'post' => $post->load('categories', 'galery'),
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Erreur lors de la mise à jour du post.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function createPost(Request $request)
    {
        // Validation des données envoyées par le front
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
            'gallery.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
            'categories' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            // Création du post sans image au départ
            $post = new Post();
            $post->user_id = Auth::id();
            $post->title = $request->title;
            $post->description = $request->description;

            // IMAGE PRINCIPALE
            if ($request->hasFile('image')) {
                $image = $request->file('image');

                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

                $destinationPath = public_path('storage/img/posts/img');

                $image->move($destinationPath, $imageName);

                $post->image = 'storage/img/posts/img/' . $imageName;
            }

            $post->save();

            // CATÉGORIES
            if ($request->filled('categories')) {
                $categories = json_decode($request->categories, true);

                if (is_array($categories) && !empty($categories)) {
                    $post->categories()->sync($categories);
                }
            }

            // GALERIE
            if ($request->hasFile('gallery')) {
                foreach ($request->file('gallery') as $galleryImage) {
                    $galleryName = time() . '_' . uniqid() . '.' . $galleryImage->getClientOriginalExtension();

                    $galleryDestinationPath = public_path('storage/img/posts/gallery');

                    $galleryImage->move($galleryDestinationPath, $galleryName);

                    Galerie::create([
                        'post_id' => $post->id,
                        'picture' => 'storage/img/posts/gallery/' . $galleryName,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Post créé avec succès.',
                'post' => $post->load('categories', 'galery'),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Erreur lors de la création du post.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


}
