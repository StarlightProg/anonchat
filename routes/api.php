<?php

use App\Http\Controllers\UserController;
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
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
