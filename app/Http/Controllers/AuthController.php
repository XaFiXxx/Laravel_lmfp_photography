<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    // Inscription avec création de token
    public function register(Request $request)
    {
        // Validation des données directement dans le contrôleur
        $validatedData = $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname'  => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users',
            'birthday'  => 'required|date',
            'password'  => 'required|string|min:8|confirmed',
            'role'      => 'required|string|in:visiteur,mannequin,photographe,organisateur',
        ]);

        // Création de l'utilisateur avec les données validées
        $user = User::create([
            'firstname' => $validatedData['firstname'],
            'lastname'  => $validatedData['lastname'],
            'email'     => $validatedData['email'],
            'birthday'  => $validatedData['birthday'],
            'password'  => Hash::make($validatedData['password']),
            'role'      => $validatedData['role'],
        ]);

        // Création du token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message'    => 'Inscription réussie!',
            'user'       => $user,
            'auth_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    // Connexion
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!auth()->attempt($credentials)) {
            return response()->json(['message' => 'Identifiants invalides.'], 401);
        }

        $user = auth()->user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message'    => 'Connexion réussie!',
            'user'       => $user,
            'auth_token' => $token,
            'token_type' => 'Bearer',
        ], 200);
    }

    // Déconnexion : Révocation du token
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Déconnexion réussie!'], 200);
    }
}
