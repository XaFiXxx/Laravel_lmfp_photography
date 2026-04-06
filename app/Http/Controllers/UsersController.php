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

    public function updateUser(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'birthday' => 'nullable|date',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        try {
            $user->firstname = $request->firstname;
            $user->lastname = $request->lastname;
            $user->email = $request->email;
            $user->birthday = $request->birthday;

            if ($request->hasFile('avatar')) {
                if ($user->avatar) {
                    $oldPath = public_path($user->avatar);

                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }

                $avatar = $request->file('avatar');
                $avatarName = time() . '_' . uniqid() . '.' . $avatar->getClientOriginalExtension();
                $destinationPath = public_path('storage/img/users');

                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }

                $avatar->move($destinationPath, $avatarName);

                $user->avatar = 'storage/img/users/' . $avatarName;
            }

            $user->save();

            return response()->json([
                'message' => 'Profil mis à jour avec succès',
                'user' => $user
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la mise à jour du profil',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function findUserById($id)
    {
        $user = User::find($id);
        return response()->json($user);
    }

    public function createUser(Request $request)
    {
        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'birthday' => 'nullable|date',
            'role' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'isAdmin' => 'nullable|boolean',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        try {
            $user = new User();
            $user->firstname = $request->firstname;
            $user->lastname = $request->lastname;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->birthday = $request->birthday;
            $user->role = $request->role;
            $user->status = $request->status;
            $user->isAdmin = $request->isAdmin ? 1 : 0;

            if ($request->hasFile('avatar')) {
                $avatar = $request->file('avatar');
                $avatarName = time() . '_' . uniqid() . '.' . $avatar->getClientOriginalExtension();

                $destinationPath = public_path('storage/img/users');

                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }

                $avatar->move($destinationPath, $avatarName);

                $user->avatar = 'storage/img/users/' . $avatarName;
            }

            $user->save();

            return response()->json([
                'message' => 'Utilisateur créé avec succès',
                'user' => $user
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la création de l\'utilisateur',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateUserDash(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // =========================
        // VALIDATION
        // =========================
        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'birthday' => 'nullable|date',
            'role' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'isAdmin' => 'nullable|boolean',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        try {

            // =========================
            // UPDATE CHAMPS
            // =========================
            $user->firstname = $request->firstname;
            $user->lastname = $request->lastname;
            $user->email = $request->email;
            $user->birthday = $request->birthday;
            $user->role = $request->role;
            $user->status = $request->status;

            // IMPORTANT : sécuriser admin
            $user->isAdmin = $request->isAdmin ? 1 : 0;

            // =========================
            // GESTION AVATAR
            // =========================
            if ($request->hasFile('avatar')) {

                // Supprimer ancien avatar
                if ($user->avatar) {
                    $oldPath = public_path($user->avatar);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }

                $avatar = $request->file('avatar');

                $avatarName = time() . '_' . uniqid() . '.' . $avatar->getClientOriginalExtension();

                $destinationPath = public_path('storage/img/users');

                $avatar->move($destinationPath, $avatarName);

                $user->avatar = 'storage/img/users/' . $avatarName;
            }

            $user->save();

            return response()->json([
                'message' => 'Utilisateur mis à jour avec succès',
                'user' => $user
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la mise à jour',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        try {

            if (auth()->id() === $user->id) {
                return response()->json([
                    'message' => 'Tu ne peux pas supprimer ton propre compte.'
                ], 403);
            }

            // =========================
            // SUPPRIMER COMMENTAIRES
            // =========================
            $user->comments()->delete();

            // =========================
            // SUPPRIMER AVATAR
            // =========================
            if ($user->avatar) {
                $avatarPath = public_path($user->avatar);
                if (file_exists($avatarPath)) {
                    unlink($avatarPath);
                }
            }

            // =========================
            // SUPPRIMER USER
            // =========================
            $user->delete();

            return response()->json([
                'message' => 'Utilisateur supprimé avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la suppression',
                'error' => $e->getMessage()
            ], 500);
        }
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
