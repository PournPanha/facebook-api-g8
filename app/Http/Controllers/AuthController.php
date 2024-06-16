<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email'     => 'required|string|max:255',
            'password'  => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'User not found'
            ], 401);
        }

        $user   = User::where('email', $request->email)->firstOrFail();
        $token  = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message'       => 'Login success',
            'access_token'  => $token,
            'token_type'    => 'Bearer'
        ]);
    }
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout successful'], 200);
    }

    // public function register(Request $request): JsonResponse
    // {
    //     $validator = Validator::make($request->all(), [
    //         'name'           => 'required|string|max:255',
    //         'email'          => 'required|string|email|max:255|unique:users',
    //         'password'       => 'required|string|min:8',
    //         'profile_image'  => 'image|mimes:jpeg,png,jpg,gif|max:2048' // Allow null or valid image file
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['error' => $validator->errors()], 400);
    //     }

    //     $profileImagePath = null;

    //     // Handle the profile image upload if exists
    //     if ($request->hasFile('profile_image')) {
    //         $profileImage = $request->file('profile_image');
    //         $profileImageName = time() . '.' . $profileImage->getClientOriginalExtension();
    //         $profileImagePath = $profileImage->storeAs('profile_images', $profileImageName, 'public');
    //     }

    //     // Create user without profile image if it doesn't exist
    //     $user = User::create([
    //         'name'           => $request->name,
    //         'email'          => $request->email,
    //         'password'       => bcrypt($request->password),
    //         'profile_image'  => $profileImagePath // Save the profile image path or null
    //     ]);

    //     $token = $user->createToken('auth_token')->plainTextToken;

    //     return response()->json([
    //         'message'       => 'User registered successfully',
    //         'access_token'  => $token,
    //         'token_type'    => 'Bearer'
    //     ], 201);
    // }
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'           => 'required|string|max:255',
            'email'          => 'required|string|email|max:255|unique:users',
            'password'       => 'required|string|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $user = User::create([
            'name'           => $request->name,
            'email'          => $request->email,
            'password'       => bcrypt($request->password)
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message'       => 'User registered successfully',
            'access_token'  => $token,
            'token_type'    => 'Bearer'
        ], 201);
    }
    public function index(Request $request)
    {
        $user = $request->user();
        $permissions = $user->getAllPermissions();
        $roles = $user->getRoleNames();
        return response()->json([
            'message' => 'Login success',
            'data' => $user,
        ]);
    }
}
