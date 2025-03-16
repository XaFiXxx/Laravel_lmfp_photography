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

    public function deleteCategory($id)
    {
        $category = Categorie::find($id);
        $category->delete();
        return response()->json('Category supprimée avec succès');
    }

    public function updateCategory(Request $request, $id)
    {
        $category = Categorie::find($id);
        $category->update($request->all());
        return response()->json('Category modifiée avec succès');
    }

    public function createCategory(Request $request)
    {
        $category = new Categorie();
        $category->name = $request->name;
        $category->save();
        return response()->json('Category créée avec succès');
    }
}
