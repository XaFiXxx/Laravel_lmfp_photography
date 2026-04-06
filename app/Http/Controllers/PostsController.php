<?php

namespace App\Http\Controllers;

use App\Models\Post; // Importation du modèle avec le nom correct (singulier)
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\Comment;
use App\Models\Galerie;
use App\Models\User;
use App\Models\Categorie;

class PostsController extends Controller
{
    // Affiche la liste des posts
    public function indexPosts()
    {
        $posts = Post::all();
        return response()->json($posts);
    }

    public function showPost($id)
    {
        $post = Post::with([
            'galery',
            'categories',
            'comments' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'comments.user'
        ])->findOrFail($id);
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

    public function dashIndexPosts()
    {
        // Récupère les posts avec leurs relations (galery et commentaires avec utilisateur)
        $posts = Post::with('galery', 'categories' , 'comments.user', 'user')->get();
        return response()->json($posts);
    }


    public function deletePost($id)
    {
        //supprime un post
        $post = Post::find($id);
        // rajouté la gestion de suppression des images
        $post->delete($post->all());
        return response()->json('post bien supprimé');
    }

    public function updatePost(Request $request, $id)
    {
        // 1) Validation
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'description'      => 'required|string',
            'image'            => 'nullable|image|max:2048',
            'categories'       => 'nullable|string',
            'removed_gallery'  => 'nullable|array',
            'removed_gallery.*'=> 'integer|exists:galery,id',
            'gallery'          => 'nullable|array',
            'gallery.*'        => 'image|max:2048',
        ]);

        $post = Post::findOrFail($id);

        // 2) Mise à jour des champs texte et image principale
        $data = [
            'title'       => $validated['title'],
            'description' => $validated['description'],
        ];
        if ($request->hasFile('image')) {
            $file     = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            // stocke dans storage/app/public/images/posts
            $file->storeAs(
                'images/posts',   // dossier relatif à storage/app/public
                $filename,
                'public'          // disque public
            );
            $data['image'] = '/storage/images/posts/' . $filename;
        }
        $post->update($data);

        // 3) Synchronisation des catégories
        if ($request->filled('categories')) {
            $cats = json_decode($validated['categories'], true);
            if (is_array($cats)) {
                $post->categories()->sync($cats);
            }
        }

        // 4) Suppression des images de galerie demandées
        if (!empty($validated['removed_gallery'])) {
            $toRemove = Galery::whereIn('id', $validated['removed_gallery'])->get();
            foreach ($toRemove as $img) {
                // supprime le fichier physique
                Storage::disk('public')->delete('images/posts/' . basename($img->picture));
                // supprime la ligne en base
                $img->delete();
            }
        }

        // 5) Ajout des nouvelles images de galerie
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                // idem : disque public
                $file->storeAs(
                    'images/posts',
                    $filename,
                    'public'
                );
                $post->galery()->create([
                    'picture' => '/storage/images/posts/' . $filename,
                ]);
            }
        }

        // Rechargement des relations et réponse
        $post->load('categories', 'galery', 'comments.user');
        return response()->json($post);
    }

    public function createPost(Request $request)
    {
        // Validation des données envoyées par le front
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'gallery.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'categories' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            // Création du post sans image au départ
            $post = new Post();
            $post->user_id = Auth::id();
            $post->title = $request->title;
            $post->description = $request->description;

            // =========================
            // IMAGE PRINCIPALE
            // =========================
            if ($request->hasFile('image')) {
                $image = $request->file('image');

                // Nom unique pour éviter les doublons
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

                // Dossier de destination
                $destinationPath = public_path('storage/img/posts/img');

                // Déplacement physique du fichier
                $image->move($destinationPath, $imageName);

                // Chemin enregistré en base
                $post->image = 'storage/img/posts/img/' . $imageName;
            }

            $post->save();

            // =========================
            // CATÉGORIES
            // =========================
            if ($request->filled('categories')) {
                $categories = json_decode($request->categories, true);

                // On vérifie que c'est bien un tableau
                if (is_array($categories) && !empty($categories)) {
                    $post->categories()->sync($categories);
                }
            }

            // =========================
            // GALERIE
            // =========================
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
