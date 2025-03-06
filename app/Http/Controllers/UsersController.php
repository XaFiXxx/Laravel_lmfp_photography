<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

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
}
