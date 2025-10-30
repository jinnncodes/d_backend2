<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Create a new user
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|string|min:6',
            'role'      => 'nullable|string',
            'branch_id' => 'nullable|integer'
        ]);

        $user = User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'password'  => Hash::make($validated['password']),
            'role'      => $validated['role'] ?? 'user',
            'branch_id' => $validated['branch_id'] ?? null,
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'data'    => $user
        ], 201);
    }

    // Update an existing user
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Optional: only allow user to update themselves or admin
        if ($user->id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name'      => 'sometimes|string',
            'email'     => 'sometimes|email|unique:users,email,' . $user->id,
            'password'  => 'sometimes|string|min:6',
            'role'      => 'sometimes|string',
            'branch_id' => 'sometimes|integer'
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'User updated successfully',
            'data'    => $user
        ]);
    }

    // Delete a user
    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Optional: only admin or self can delete
        if ($user->id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}
