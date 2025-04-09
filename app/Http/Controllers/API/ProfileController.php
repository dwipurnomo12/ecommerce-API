<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserCollection;

class ProfileController extends Controller
{
    public function index()
    {
        return ApiResponse::success(new UserResource(auth()->user()), 'Show profile user.');
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $validated = $request->validate([
            'name'     => 'nullable|string|max:255',
            'email'    => 'nullable|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $updateData = array_filter($request->only(['name', 'email']));

        if (!empty($request->password)) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->fill($updateData)->save();

        return ApiResponse::success(new UserResource($user), 'Profile updated successfully.');
    }
}