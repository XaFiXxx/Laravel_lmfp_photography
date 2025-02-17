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
}
