<?php

use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\FriendController;
use App\Http\Controllers\Api\FriendRequestController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\PasswordResetController;
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

// user route
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [UserController::class, 'show'])->name('profile.show');
    Route::post('/profile/update', [UserController::class, 'update'])->name('profile.update');

    Route::post('/friends/{user}/request', [FriendRequestController::class, 'sendRequest']);
    Route::post('/friends/{user}/accept', [FriendRequestController::class, 'acceptRequest']);
    Route::post('/friends/{user}/decline', [FriendRequestController::class, 'declineRequest']);
    Route::get('/friends/list/requests', [FriendRequestController::class, 'index']);
    Route::delete('/friends/{user}', [FriendRequestController::class, 'remove']);
});

// post route
Route::prefix('post')->group(function () {
    Route::get('/list', [PostController::class, 'index'])->middleware('auth:sanctum');
    Route::post('/create', [PostController::class, 'store'])->middleware('auth:sanctum');
    Route::get('/show/{id}', [PostController::class, 'show']);
    Route::put('/update/{id}', [PostController::class, 'update']);
    Route::delete('/delete/{id}', [PostController::class, 'destroy']);
});

// Comment routes
Route::get('/post/{id}/comment', [CommentController::class, 'index'])->middleware('auth:sanctum');
Route::post('/post/{id}/comment', [CommentController::class, 'store'])->middleware('auth:sanctum');
Route::delete('/comment/{comment}', [CommentController::class, 'destroy'])->middleware('auth:sanctum');
Route::post('/register', [AuthController::class,'register']);


// Route::post('/password/email', [PasswordResetController::class, 'sendResetLinkEmail']);
// Route::post('/password/reset', [PasswordResetController::class, 'resetPassword']);

// Like route
Route::middleware('auth:sanctum')->group(function () {
    Route::post('posts/{post}/like', [LikeController::class, 'likePost']);
    Route::delete('posts/{post}/unlike', [LikeController::class, 'unlikePost']);
    Route::get('posts/{post}/likes', [LikeController::class, 'getPostLikes']); 
});

// Route::middleware('auth')->group(function () {
//     // Friends
//     Route::post('/friends/{user}/request', [FriendController::class, 'sendRequest']);
//     Route::post('/friends/{user}/accept', [FriendController::class, 'acceptRequest']);
//     Route::post('/friends/{user}/decline', [FriendController::class, 'declineRequest']);
//     Route::get('/friends', [FriendController::class, 'index']);
//     Route::delete('/friends/{user}', [FriendController::class, 'remove']);
// });