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
}
