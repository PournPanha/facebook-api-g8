<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Password;

class PasswordResetController extends Controller
{
    // Method to send reset link email
    public function sendResetLinkEmail(Request $request)
    {
        // Validate email input
        $request->validate([
            'email' => 'required|email',
        ]);

        // Find the user by email
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Send reset link to the user's email
        $response = Password::sendResetLink($request->only('email'));

        if ($response == Password::RESET_LINK_SENT) {
            // Optionally send a message via Facebook Messenger if the user has a Facebook ID
            if ($user->facebook_id) {
                Http::post('https://graph.facebook.com/v10.0/me/messages', [
                    'recipient' => ['id' => $user->facebook_id],
                    'message' => ['text' => 'Your password reset link has been sent to your email address.'],
                    'access_token' => env('FACEBOOK_APP_SECRET')
                ]);
            }
            return response()->json(['message' => 'Reset link sent successfully'], 200);
        } else {
            return response()->json(['error' => 'Unable to send reset link'], 500);
        }
    }

    // Method to reset password
    public function resetPassword(Request $request)
    {
        // Validate input data
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        // Reset the password
        $response = Password::reset($request->only('email', 'password', 'password_confirmation', 'token'), function ($user, $password) {
            $user->password = Hash::make($password);
            $user->save();
        });

        if ($response == Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Password reset successfully'], 200);
        } else {
            return response()->json(['error' => 'Failed to reset password'], 500);
        }
    }
}
