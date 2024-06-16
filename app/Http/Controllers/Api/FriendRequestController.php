<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FriendRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FriendRequestController extends Controller
{
    public function sendRequest(User $user)
    {
        $sender = Auth::user();

        if ($sender->id === $user->id) {
            return response()->json(['message' => 'You cannot send a friend request to yourself'], 400);
        }

        $friendRequest = FriendRequest::create([
            'sender_id' => $sender->id,
            'receiver_id' => $user->id,
            'status' => 'pending',
        ]);

        return response()->json(['message' => 'Friend request sent successfully']);
    }

    public function acceptRequest(User $user)
    {
        $receiver = Auth::user();
        $friendRequest = FriendRequest::where('sender_id', $user->id)
            ->where('receiver_id', $receiver->id)
            ->where('status', 'pending')
            ->first();

        if (!$friendRequest) {
            return response()->json(['message' => 'Friend request not found'], 404);
        }

        $friendRequest->update(['status' => 'accepted']);

        return response()->json(['message' => 'Friend request accepted successfully']);
    }

    public function declineRequest(User $user)
    {
        $receiver = Auth::user();
        $friendRequest = FriendRequest::where('sender_id', $user->id)
            ->where('receiver_id', $receiver->id)
            ->where('status', 'pending')
            ->first();

        if (!$friendRequest) {
            return response()->json(['message' => 'Friend request not found'], 404);
        }

        $friendRequest->update(['status' => 'rejected']);

        return response()->json(['message' => 'Friend request rejected successfully']);
    }

    public function index(){
        $user = Auth::user();
        $friendRequests = FriendRequest::where('receiver_id', $user->id)->get();

        return response()->json($friendRequests, 200);
    }


    public function remove(User $user)
    {
        $authUser = Auth::user();

        $friendRequest = FriendRequest::where(function($query) use ($authUser, $user) {
            $query->where('sender_id', $authUser->id)->where('receiver_id', $user->id);
        })->orWhere(function($query) use ($authUser, $user) {
            $query->where('sender_id', $user->id)->where('receiver_id', $authUser->id);
        })->where('status', 'accepted')->first();

        if (!$friendRequest) {
            return response()->json(['message' => 'Friendship not found'], 404);
        }

        $friendRequest->delete();

        return response()->json(['message' => 'Friend removed successfully']);
    }
}
