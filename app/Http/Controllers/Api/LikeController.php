<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function likePost($postId)
    {
        $post = Post::findOrFail($postId);
        $user = Auth::user();
        if ($post->likes()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'Already liked'], 400);
        }
        $post->likes()->attach($user->id);
        return response()->json(['message' => 'Post liked successfully']);
    }

    public function unlikePost($postId)
    {
        $post = Post::findOrFail($postId);
        $user = Auth::user();
        if (!$post->likes()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'Not liked yet'], 400);
        }
        $post->likes()->detach($user->id);
        return response()->json(['message' => 'Post unliked successfully']);
    }
    public function getPostLikes($postId)
    {
        $post = Post::findOrFail($postId);
        $users = $post->likes()->select('users.id', 'users.name')->get();
        return response()->json($users);
    }
}
