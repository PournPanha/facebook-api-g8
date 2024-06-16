<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Js;

class UserController extends Controller
{
    public function show(Request $request)
    {
        if (Auth::check()) {
            return response()->json(Auth::user());
        } else {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
    }

    /**
     * Update the user's profile.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name'           => 'required|string|max:255',
            'email'          => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'profile_image'  => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        if ($request->hasFile('profile_image')) {
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }

            $profileImage = $request->file('profile_image');
            $profileImageName = time() . '.' . $profileImage->getClientOriginalExtension();
            $profileImagePath = $profileImage->storeAs('profile_images', $profileImageName, 'public');

            $user->profile_image = $profileImagePath;
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return response()->json(['message' => 'Profile updated successfully']);
    }
}
