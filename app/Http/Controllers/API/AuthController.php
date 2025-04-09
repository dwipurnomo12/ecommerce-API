<?php

namespace App\Http\Controllers\API;

use App\Helpers\ApiResponse;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
        ]);

        $user->assignRole('Customer');
        $token = $user->createToken('auth_token')->plainTextToken;

        $data = [
            'token' => $token,
            'user'  => new UserResource($user)
        ];


        return ApiResponse::success($data, 'Registration Successfully');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return ApiResponse::error('Unauthorized', 401);
        }

        $tokenResult = $user->createToken('auth_token');
        $accessToken = $tokenResult->accessToken;

        $expiresAt = Carbon::now()->addDay();
        $accessToken->expires_at = $expiresAt;
        $accessToken->save();

        $data = [
            'user'       => new UserResource($user),
            'token'      => $tokenResult->plainTextToken,
            'expires_at' => $expiresAt->toDateTimeString(),
        ];

        return ApiResponse::success($data, 'Login Successfully');
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return ApiResponse::success(null, 'Logout successfully');
    }
}