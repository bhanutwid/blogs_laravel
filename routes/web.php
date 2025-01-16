<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

// require __DIR__.'/api.php';

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/', function () {
//     return response()->json(['message' => 'Hello, World!']);
// });

// Route::get('posts', [PostController::class, 'index']);
// Route::post('posts', [PostController::class, 'store']);
// Route::get('posts/{id}', [PostController::class, 'show']);
// Route::put('posts/{id}', [PostController::class, 'update']);
// Route::delete('posts/{id}', [PostController::class, 'destroy']);