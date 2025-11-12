<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only(['email', 'password']) ?: $request->only(['phone', 'password']);
        
        $user = User::where('email', $credentials['email'] ?? null)
            ->orWhere('phone', $credentials['phone'] ?? null)
            ->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'status' => 'error',
                'code' => 401,
                'message' => 'Invalid credentials',
                'errors' => ['credentials' => ['The provided credentials are incorrect.']]
            ], 401);
        }

        if ($user->status !== 'active') {
            return response()->json([
                'status' => 'error',
                'code' => 403,
                'message' => 'Account is not active',
                'errors' => []
            ], 403);
        }

        $user->update(['last_login_at' => now()]);
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'uuid' => $user->uuid,
                    'name' => $user->full_name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'role' => $user->role->slug,
                ],
                'token' => $token,
            ],
            'errors' => []
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'data' => ['message' => 'Logged out successfully'],
            'errors' => []
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load('role');

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'uuid' => $user->uuid,
                    'name' => $user->full_name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'role' => $user->role->slug,
                    'status' => $user->status,
                    'last_login_at' => $user->last_login_at,
                ],
            ],
            'errors' => []
        ]);
    }
}
