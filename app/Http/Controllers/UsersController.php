<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class UsersController extends Controller
{

    public function index()
    {
        $users = User::all();
        return  response()->json($users);
    }

    public function findUserById($id)
    {
        $user = User::find($id);
        return response()->json($user);
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::find($id);
        $user->update($request->all());
        return response()->json($user);
    }

    public function deleteUser($id)
    {
        $user = user::find($id);
        $user->delete($user->all());
        return response()->json('Utilisateur bien supprimé');
    }

    public function updatePassword(Request $request, $id)
    {
        // Valider les données envoyées
        $request->validate([
            'oldPassword' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        // Récupérer l'utilisateur, ou retourner une erreur si non trouvé
        $user = User::findOrFail($id);

        // Vérifier que l'ancien mot de passe fourni correspond au mot de passe actuel
        if (!Hash::check($request->oldPassword, $user->password)) {
            return response()->json(['error' => 'Ancien mot de passe incorrect'], 400);
        }

        // Mettre à jour le mot de passe en le hachant
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json(['message' => 'Mot de passe mis à jour avec succès'], 200);
    }

}
