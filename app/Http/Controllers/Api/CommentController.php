<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $comments = Comment::where('post_id', $id)->get();
        return response()->json(['success' => true, 'data' => $comments], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'content' => 'required|string',
        ]);

        $comment = Comment::create([
            'post_id' => $request->id,
            'content' => $request->input('content'),
            'auth_id' => $user->id
        ]);

        return response()->json(['success' => true, 'data' => $comment], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        $comment->delete();
        return response()->json(['success' => true, 'message' => 'Comment deleted successfully'], 200);
    }
}
