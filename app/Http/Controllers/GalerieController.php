<?php

namespace App\Http\Controllers;

use App\Models\Galerie;
use Illuminate\Http\Request;

class GalerieController extends Controller
{
    public function index(){
        $galerie = Galerie::all();

        return response()->json($galerie);
    }



     // ------------------ DASHBOARD ------------------ //

     public function indexDash(){
        $galerie = Galerie::with('post')->get();

        return response()->json($galerie);
    }
}
