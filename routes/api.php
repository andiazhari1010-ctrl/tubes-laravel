<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PomodoroController;
use App\Http\Controllers\Api\NoteController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\FriendController;
use App\Http\Controllers\Api\InviteController; // Pastikan ini ada
use App\Http\Controllers\Api\NotificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes (Butuh Login)
Route::middleware('auth:sanctum')->group(function () {
    
    // User Info
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);

    // Pomodoro
    Route::apiResource('pomodoros', PomodoroController::class);

    // Notes
    Route::apiResource('notes', NoteController::class);

    // Events (Kalender)
    Route::apiResource('events', EventController::class);

    // Friends (Tambah Teman)
    Route::get('/friends/search', [FriendController::class, 'search']);
    Route::post('/friends/add/{id}', [FriendController::class, 'addFriend']);
    Route::get('/friends/requests', [FriendController::class, 'getRequests']);
    Route::post('/friends/accept/{id}', [FriendController::class, 'acceptFriend']); // Route Accept Friend
    Route::post('/friends/reject/{id}', [FriendController::class, 'rejectFriend']); // Route Reject Friend
    Route::get('/friends/list', [FriendController::class, 'myFriends']);

    // Invites (Undangan Event) - INI YANG PENTING UNTUK ACCEPT
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/invite/{id}', [InviteController::class, 'respond']); // <--- INI SOLUSINYA

    Route::middleware('auth:sanctum')->group(function () {
    Route::post('/invite/{id}', [InviteController::class, 'respond']);
});

});