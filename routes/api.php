<?php

use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\FriendRequestController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


// Authentication routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/me', [AuthController::class, 'index'])->middleware('auth:sanctum');

Route::post('password/email', [AuthController::class, 'forgotPassword']);
Route::post('password/reset', [AuthController::class, 'resetPassword']);

// user route
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [UserController::class, 'show'])->name('profile.show');
    Route::post('/profile/update', [UserController::class, 'update'])->name('profile.update');


    // firend request
    Route::post('/friends/{user}/request', [FriendRequestController::class, 'sendRequest']);
    Route::post('/friends/{user}/accept', [FriendRequestController::class, 'acceptRequest']);
    Route::post('/friends/{user}/reject', [FriendRequestController::class, 'declineRequest']);
    Route::get('/friends/list/requests', [FriendRequestController::class, 'index']);
    Route::delete('/friends/{user}/remove', [FriendRequestController::class, 'remove']);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/post/list', [PostController::class, 'index']);
    Route::post('/post/create', [PostController::class, 'store']);
    Route::get('/post/show/{id}', [PostController::class, 'show']);
    Route::put('/post/update/{id}', [PostController::class, 'update']);
    Route::delete('/post/delete/{id}', [PostController::class, 'destroy']);

    Route::post('/posts/{postId}/comments', [PostController::class, 'addComment']);
    Route::get('/posts/{postId}/comments', [PostController::class, 'getComments']);
    Route::delete('/posts/{postId}/comments/{commentId}' , [PostController::class, 'deleteComment']);

    Route::post('/posts/{postId}/like', [PostController::class, 'likePost']);
    Route::delete('/posts/{postId}/unlike', [PostController::class, 'unlikePost']);
    Route::get('/posts/{postId}/likes', [PostController::class, 'getLikes']);
});

