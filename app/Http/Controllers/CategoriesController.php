<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Categorie;

class CategoriesController extends Controller
{
    public function indexCategories()
    {
        $categories = Categorie::all();
        return response()->json($categories);
    }

    public function showCategory($id)
    {
        $category = Categorie::find($id);
        return response()->json($category);
    }

    // ------------------ DASHBOARD ------------------ //

    public function createCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        try {
            $category = new Categorie();
            $category->name = $request->name;
            $category->save();

            return response()->json([
                'message' => 'Catégorie créée avec succès',
                'category' => $category,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la création de la catégorie',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateCategory(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
        ]);

        try {
            $category = Categorie::findOrFail($id);

            $category->name = $request->name;
            $category->save();

            return response()->json([
                'message' => 'Catégorie modifiée avec succès',
                'category' => $category,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la modification de la catégorie',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteCategory($id)
    {
        try {
            $category = Categorie::findOrFail($id);

            // Si la catégorie est liée à des posts, on détache avant suppression
            $category->posts()->detach();

            $category->delete();

            return response()->json([
                'message' => 'Catégorie supprimée avec succès',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la suppression de la catégorie',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
