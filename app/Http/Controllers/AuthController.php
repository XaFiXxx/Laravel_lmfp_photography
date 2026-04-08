<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname'  => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users',
            'birthday'  => 'required|date',
            'password'  => 'required|string|min:8|confirmed',
            'role'      => 'required|string|in:visiteur,mannequin,photographe,organisateur',
        ]);

        $user = User::create([
            'firstname' => $validatedData['firstname'],
            'lastname'  => $validatedData['lastname'],
            'email'     => $validatedData['email'],
            'birthday'  => $validatedData['birthday'],
            'password'  => Hash::make($validatedData['password']),
            'role'      => $validatedData['role'],
        ]);

        $user->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Inscription réussie. Vérifiez votre email pour activer votre compte.',
            'user'    => $user,
        ], 201);
    }

    // Connexion
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!auth()->attempt($credentials)) {
            return response()->json([
                'message' => 'Identifiants invalides.'
            ], 401);
        }

        $user = auth()->user();

        if (! $user->hasVerifiedEmail()) {
            auth()->logout();

            return response()->json([
                'message' => 'Votre adresse email n’a pas encore été vérifiée.'
            ], 403);
        }

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

    public function resendVerificationEmail(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $validatedData['email'])->first();

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Cette adresse email est déjà vérifiée.'
            ], 400);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Email de confirmation renvoyé avec succès.'
        ], 200);
    }


    // ------------ RESET PASSWORD ------------ //

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => 'Si un compte existe avec cette adresse, un lien de réinitialisation a été envoyé.'
            ], 200);
        }

        return response()->json([
            'message' => 'Si un compte existe avec cette adresse, un lien de réinitialisation a été envoyé.'
        ], 200);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'Mot de passe réinitialisé avec succès.'
            ], 200);
        }

        return response()->json([
            'message' => 'Le lien de réinitialisation est invalide ou expiré.'
        ], 400);
    }


    // ------------ DASHBOARD ------------ //

    public function dashLogin(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!auth()->attempt($credentials)) {
            return response()->json([
                'message' => 'Identifiants invalides.'
            ], 401);
        }

        $user = auth()->user();

        if (! $user->hasVerifiedEmail()) {
            auth()->logout();

            return response()->json([
                'message' => 'Votre adresse email n’a pas encore été vérifiée.'
            ], 403);
        }

        if ($user->isAdmin != 1) {
            auth()->logout();

            return response()->json([
                'message' => 'Accès non autorisé.'
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message'    => 'Connexion réussie!',
            'user'       => $user,
            'auth_token' => $token,
            'token_type' => 'Bearer',
        ], 200);
    }


    public function dashLogout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Déconnexion réussie!'], 200);
    }
}
