<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $posts = Post::list($request);
        return response()->json(['success' => true, 'data' => $posts], 200);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $post = Post::store($request);
        return response()->json(['success' => true, 'data' => $post], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        return response()->json(['success' => true, 'data' => $post], 200);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $updatedPost = Post::store($request, $post->id);
        return response()->json(['success' => true, 'data' => $updatedPost], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $post->delete();
        return response()->json(['success' => true, 'message' => 'Post deleted successfully'], 200);
    }
}
