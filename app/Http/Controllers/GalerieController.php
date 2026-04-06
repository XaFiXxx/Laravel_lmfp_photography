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

    public function delete($id)
    {
        try {
            // Récupérer l'image galerie
            $image = Galerie::findOrFail($id);

            // =========================
            // SUPPRESSION FICHIER
            // =========================
            if ($image->picture) {
                $filePath = public_path($image->picture);

                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            // =========================
            // SUPPRESSION DB
            // =========================
            $image->delete();

            return response()->json([
                'message' => 'Image supprimée avec succès'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la suppression',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
