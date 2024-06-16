<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function index($id)
    {
        $comments = Comment::where('post_id', $id)->get();
        return response()->json(['success' => true, 'data' => $comments], 200);
    }
    // public function store(Request $request)
    // {

    //     $validator = Validator::make($request->all(), [
    //         'post_id' => 'required|exists:posts,id',
    //         'content' => 'required|string',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
    //     }
    //     $user = Auth::user();
    //     $comment = Comment::create([
    //         'post_id' => $request->input('post_id'),
    //         'content' => $request->input('content'),
    //         'user_id' => $user->id,
    //     ]);
    //     $comment->load('post', 'user');
    //     return response()->json(['success' => true, 'data' => $comment], 201);
    // }

    public function store(Request $request)
    {
        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|exists:posts,id',
            'content' => 'required|string',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
        }

        // Retrieve the authenticated user
        $user = Auth::user();

        // Create a new comment associated with the authenticated user
        $comment = Comment::create([
            'post_id' => $request->input('post_id'),
            'content' => $request->input('content'),
            'user_id' => $user->id,
        ]);

        // Eager load the 'post' and 'user' relationships
        $comment->load('post', 'user');

        // Return JSON response with success message and the newly created comment
        return response()->json(['success' => true, 'data' => $comment], 201);
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();
        return response()->json(['success' => true, 'message' => 'Comment deleted successfully'], 200);
    }
}
