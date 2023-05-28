<?php

use App\Http\Controllers\ChatsController;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\PostsController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Auth/Login');
})
->middleware('guest');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::prefix('/posts')->name('posts')->group(function(){
        Route::get('/', [PostsController::class, 'index']);
        Route::post('/', [PostsController::class, 'store']);
        Route::post('/{post}/update', [PostsController::class, 'update'])->name('.update');
        Route::get('/get-data', [PostsController::class, 'getData'])->name('.getData');
    });

    Route::prefix('/chats')->name('chats')->group(function() {
        Route::post('/{post}', [ChatsController::class, 'store']);
    });

    Route::prefix('/messages')->name('messages')->group(function() {
        Route::get('/{chat}', [MessagesController::class, 'index']);
        Route::post('/{chat}', [MessagesController::class, 'store']);
    });
});
