<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\UserController;
use App\Jobs\ProcessChatMessage;
use Illuminate\Http\Request;
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

Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('/register', [UserController::class, 'register']);//->middleware('throttle:2,5');
    Route::post('/login', [UserController::class, 'login']);//->middleware('throttle:2,5');
    Route::get('/users', [UserController::class, 'users']);//->middleware('throttle:2,5');
    Route::get('/chats', [UserController::class, 'chats']);//->middleware('throttle:2,5');
    Route::get('/requests', [UserController::class, 'requests']);//->middleware('throttle:2,5');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::group([
        'prefix' => 'chat'
    ], function () {
        Route::post('/request', [ChatController::class, 'create_chat_request']);
        Route::post('/create', [ChatController::class, 'create_chat']);
        Route::get('/list', [ChatController::class, 'chat_list'])->middleware('route_paginate');

        Route::middleware('client_in_chat')->group(function () {
            Route::post('/send_message', [ChatController::class, 'send_message']);

            Route::get('/{group_id}', [ChatController::class, 'chat_data'])->middleware('route_paginate');
        });
    });
});


