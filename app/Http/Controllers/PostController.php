<?php

namespace App\Http\Controllers;

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
}
