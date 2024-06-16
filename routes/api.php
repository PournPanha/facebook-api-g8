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

Route::get('/me', [AuthController::class, 'index'])->middleware('auth:sanctum');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/password/reset', [AuthController::class, 'resetPassword']);


/// post routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/post/list', [PostController::class, 'index']);
    Route::post('/post/create', [PostController::class, 'store']);
    Route::get('/post/show/{id}', [PostController::class, 'show']);
    Route::put('/post/update/{id}', [PostController::class, 'update']);
    Route::delete('/post/delete/{id}', [PostController::class, 'destroy']);
});

Route::get('/post/{id}/comment', [CommentController::class, 'index'])->middleware('auth:sanctum');
Route::post('/comments', [CommentController::class, 'store'])->middleware('auth:sanctum');
Route::delete('/comment/{comment}', [CommentController::class, 'destroy'])->middleware('auth:sanctum');
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user/profile', [UserController::class, 'profile']);
    Route::put('/user/profile/update', [UserController::class, 'updateProfile']);
    Route::post('/user/profile/picture', [UserController::class, 'uploadProfileImage']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('posts/{post}/like', [LikeController::class, 'likePost']);
    Route::delete('posts/{post}/unlike', [LikeController::class, 'unlikePost']);
    Route::get('posts/{post}/likes', [LikeController::class, 'getPostLikes']);
});


Route::post('send-friend-request', [FriendRequestController::class, 'sendFriendRequest']);
