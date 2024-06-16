<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $posts = $user->posts->all($request);
        return response()->json(['success' => true, 'data' => $posts], 200);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'content' => 'required|string',
            'tags' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
        }
        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
        }
        $post = Post::create([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'image' => $request->input('image', ''),
            'video' => $request->input('video', ''),
            'user_id' => $userId,
            'tags' => $request->input('tags'),
        ]);
        return response()->json(['success' => true, 'data' => $post], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = Auth::user();
        $post = Post::find($id);
        if (!$post) {
            return response()->json(['success' => false, 'message' => 'Post not found'], 404);
        }
        if ($post->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
        }
        return response()->json(['success' => true, 'data' => $post], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $post = Post::find($id);
        if (!$post) {
            return response()->json(['success' => false, 'message' => 'Post not found'], 404);
        }
        if ($post->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
        }
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'content' => 'required|string',
            'tags' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
        }
        $post->title = $request->input('title');
        $post->content = $request->input('content');
        $post->tags = $request->input('tags');
        $post->save();
        return response()->json(['success' => true, 'data' => $post], 200);
    }


    /**
     * Remove the specified resource from storage.
     */

    public function destroy($id)
    {
        $user = Auth::user();
        $post = Post::find($id);
        if (!$post) {
            return Response::json(['success' => false, 'message' => 'Post not found'], 404);
        }
        if ($post->user_id !== $user->id) {
            return Response::json(['success' => false, 'message' => 'Unauthorized access'], 403);
        }
        $post->delete();
        return Response::json(['success' => true, 'message' => 'Post deleted successfully'], 200);
    }

    public function addComment(Request $request, $postId)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
        }

        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
        }

        $post = Post::find($postId);
        if (!$post) {
            return response()->json(['success' => false, 'message' => 'Post not found'], 404);
        }

        $comment = Comment::create([
            'post_id' => $postId,
            'user_id' => $user->id,
            'content' => $request->input('content'),
        ]);

        return response()->json(['success' => true, 'data' => $comment], 201);
    }

    /**
     * Get comments for a post.
     */
    public function getComments($postId)
    {
        $post = Post::find($postId);
        if (!$post) {
            return response()->json(['success' => false, 'message' => 'Post not found'], 404);
        }

        $comments = $post->comments;

        return response()->json(['success' => true, 'data' => $comments], 200);
    }

    public function deleteComment($postId, $commentId)
    {
        $user = Auth::user();
        $comment = Comment::where('post_id', $postId)->where('id', $commentId)->first();

        if (!$comment) {
            return response()->json(['success' => false, 'message' => 'Comment not found'], 404);
        }

        if ($comment->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
        }

        $comment->delete();

        return response()->json(['success' => true, 'message' => 'Comment deleted successfully'], 200);
    }

    public function likePost($postId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
        }

        $post = Post::find($postId);
        if (!$post) {
            return response()->json(['success' => false, 'message' => 'Post not found'], 404);
        }

        $like = Like::firstOrCreate([
            'post_id' => $postId,
            'user_id' => $user->id,
        ]);

        return response()->json(['success' => true, 'data' => $like], 201);
    }

    /**
     * Unlike a post.
     */
    public function unlikePost($postId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
        }

        $post = Post::find($postId);
        if (!$post) {
            return response()->json(['success' => false, 'message' => 'Post not found'], 404);
        }

        $like = Like::where('post_id', $postId)->where('user_id', $user->id)->first();
        if (!$like) {
            return response()->json(['success' => false, 'message' => 'Like not found'], 404);
        }

        $like->delete();

        return response()->json(['success' => true, 'message' => 'Unlike successfully'], 200);
    }
    public function getLikes($postId)
    {
        $post = Post::find($postId);
        if (!$post) {
            return response()->json(['success' => false, 'message' => 'Post not found'], 404);
        }

        $likes = $post->likes()->with('user')->get();

        return response()->json(['success' => true, 'data' => $likes], 200);
    }


}
