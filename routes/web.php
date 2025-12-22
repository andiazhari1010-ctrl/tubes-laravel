<?php

use Illuminate\Support\Facades\Route;

// --- IMPORT CONTROLLERS ---
use App\Http\Controllers\AuthController; 
use App\Http\Controllers\NoteController;
use App\Http\Controllers\Api\PomodoroController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\InviteController;

// [PENTING] Kita bedakan nama Controller View dan Controller API
use App\Http\Controllers\FriendController as FriendViewController; 
use App\Http\Controllers\Api\FriendController as FriendApiController; 


// --- PUBLIC ---
Route::get('/', function () { return view('home'); });
Route::get('/login', function () { return view('login'); })->name('login');
Route::get('/register', function () { return view('register'); });
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout']);


// --- AUTHENTICATED USER ---
Route::middleware('auth')->group(function () {
    
    // VIEWS (Tampilan)
    Route::get('/index', function () { return view('index'); });
    Route::get('/pomodoro', function () { return view('pomodoro'); });
    Route::get('/calendar', function () { return view('calendar'); });
    Route::get('/notes', function () { return view('notes'); });
    Route::get('/info', function () { return view('info'); });

    // Halaman Notifikasi (Pastikan nama file: notifications.blade.php)
    Route::get('/notifications', [NotificationController::class, 'listAll']);

    // Halaman Add Friend (Menggunakan Controller View)
    Route::get('/add-friend', [FriendViewController::class, 'index']);


    // FITUR NOTES
    Route::get('/notes/data', [NoteController::class, 'index']);
    Route::post('/notes/save', [NoteController::class, 'store']);
    Route::delete('/notes/delete/{id}', [NoteController::class, 'destroy']);

    // FITUR POMODORO
    Route::post('/api/log', [PomodoroController::class, 'storeLog']);
    Route::get('/api/stats', [PomodoroController::class, 'getStats']);

    // FITUR SEARCH
    Route::get('/api/search', [SearchController::class, 'search']);

    // FITUR FRIEND (Menggunakan Controller API)
    Route::post('/api/friend/add', [FriendApiController::class, 'addFriend']);
    Route::get('/api/friend/requests', [FriendApiController::class, 'getIncomingRequests']);
    Route::get('/api/friend/list', [FriendApiController::class, 'getMyFriends']);
    Route::post('/api/friend/accept/{id}', [FriendApiController::class, 'acceptFriend']);
    Route::post('/api/friend/reject/{id}', [FriendApiController::class, 'rejectFriend']);
    // API UNFRIEND (Baru)
    Route::post('/api/friend/unfriend', [FriendApiController::class, 'unfriend']);

    // FITUR NOTIFICATIONS
    Route::get('/api/notifications', [NotificationController::class, 'index']);
    Route::post('/api/notifications/{id}/read', [NotificationController::class, 'markRead']);

    // FITUR CALENDAR & EVENTS
    Route::get('/api/events', [EventController::class, 'index']);
    Route::post('/api/events', [EventController::class, 'store']);
    Route::delete('/api/events/{id}', [EventController::class, 'destroy']);

    // FITUR INVITE
    Route::post('/api/invite/{id}', [InviteController::class, 'respond']);

});