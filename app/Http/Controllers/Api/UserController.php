<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function profile(Request $request)
    {
        if (Auth::check()) {
            return $request->user();
        } else {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
    }
    public function updateProfile(Request $request)
    {
        if (Auth::check()) {
            $user = $request->user();
            $user->update($request->all());
            return response()->json(['message' => 'Profile updated successfully', 'user' => $user]);
        } else {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
    }

    public function uploadProfileImage(Request $request)
    {
        if (Auth::check()) {
            $user = $request->user();

            if ($request->hasFile('profile_image')) {
                $file = $request->file('profile_image');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('profile_images', $fileName, 'public');

                // Update user's profile image path in the database
                $user->profile_image = $filePath;
                $user->save();

                return response()->json(['message' => 'Profile image uploaded successfully']);
            } else {
                return response()->json(['error' => 'No file uploaded'], 400);
            }
        } else {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
    }

    

    
}
