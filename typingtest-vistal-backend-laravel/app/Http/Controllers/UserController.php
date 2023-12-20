<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function store(Request $request)
{
    $fields = $request->validate([
        'name' => 'required|string|max:255',
    ]);

    $user = User::create($fields);

    return response()->json(['message' => 'User created successfully', 'user' => $user]);
}

public function index()
{
    $users = User::orderBy('updated_at', 'desc')->get();

    $userNames = $users->pluck('name');

    return response()->json(['data' => $userNames]);
}

public function getUserById($id)
{
    $user = User::find($id);

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    return response()->json($user);
}
}
