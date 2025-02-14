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
}
