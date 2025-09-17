<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    //

    /**
     * Register a new user.
     */
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name'     => 'required|string|max:255',
                'email'    => 'required|string|email|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'status'  => true,
                'message' => 'User registered successfully.',
                'data'    => [
                    'user'  => new UserResource($user),
                    'token' => $token,
                ],
            ], 201);
        } catch (\Exception $e) {
            // Catch any unexpected error
            return response()->json([
                'success'  => false,
                'message' => 'Something went wrong while User registered.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Login and issue a token.
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email'    => 'required|string|email',
                'password' => 'required|string',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user || ! Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }

            //revoke old tokens
            $user->tokens()->delete();

            //create new token
            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'success'  => true,
                'message' => 'Login successful.',
                'data'    => [
                    'user'  => new UserResource($user),
                    'token' => $token,
                ],
            ],201);
        } catch (\Exception $e) {
            // Catch any unexpected error
            return response()->json([
                'success'  => false,
                'message' => 'Something went wrong while Login.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Logout (revoke token).
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success'  => true,
                'message' => 'Logged out successfully.',
            ]);
        } catch (\Exception $e) {
            // Catch any unexpected error
            return response()->json([
                'success'  => false,
                'message' => 'Something went wrong while Logged out.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
