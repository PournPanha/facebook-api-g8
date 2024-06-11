<?php

use App\Http\Controllers\Api\CommentController;
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

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/post/list',[PostController::class,'index'])->middleware('auth:sanctum');
Route::post('/post/create', [PostController::class, 'store'])->middleware('auth:sanctum');
Route::get('/post/show/{id}',[PostController::class, 'show']);
Route::put('/post/update/{id}', [PostController::class, 'update']);
Route::delete('/post/delete/{id}', [PostController::class, 'destroy']);

Route::get('/post/{id}/comment', [CommentController::class, 'index'])->middleware('auth:sanctum');
Route::post('/post/{id}/comment', [CommentController::class, 'store'])->middleware('auth:sanctum');
Route::delete('/comment/{comment}', [CommentController::class, 'destroy'])->middleware('auth:sanctum');