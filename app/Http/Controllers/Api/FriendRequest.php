<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FriendRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FriendRequestController extends Controller
{
    public function sendFriendRequest(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id',
        ]);

        $senderId = Auth::id();
        $recipientId = $request->recipient_id;

        // Check if a request already exists
        $existingRequest = FriendRequest::where('sender_id', $senderId)
            ->where('recipient_id', $recipientId)
            ->first();

        if ($existingRequest) {
            return response()->json(['message' => 'Friend request already sent.'], 400);
        }

        // Create new friend request
        $friendRequest = FriendRequest::create([
            'sender_id' => $senderId,
            'recipient_id' => $recipientId,
            'status' => 'pending', // pending, accepted, declined
        ]);

        return response()->json(['message' => 'Friend request sent successfully.']);
    }
}
