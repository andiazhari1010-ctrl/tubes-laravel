<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController; // Panggil Controller

// Halaman Depan
Route::get('/', function () { return view('home'); });

// --- AUTHENTICATION ROUTES ---
// Tampilkan Form
Route::get('/login', function () { return view('login'); })->name('login');
Route::get('/register', function () { return view('register'); });

// Proses Form (POST)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout']); // Kita pakai GET untuk logout simpel

// --- HALAMAN YANG BUTUH LOGIN (DIPROTEKSI) ---

Route::middleware('auth')->group(function () {
    Route::get('/index', function () { return view('index'); });
    Route::get('/pomodoro', function () { return view('pomodoro'); });
    Route::get('/calendar', function () { return view('calendar'); });
    Route::get('/notes', function () { return view('notes'); });
    Route::get('/notification', function () { return view('notification'); });
    Route::get('/add-friend', function () { return view('add-friend'); });
    Route::get('/info', function () { return view('info'); });
    Route::get('/notes/data', [App\Http\Controllers\NoteController::class, 'index']);
    Route::post('/notes/save', [App\Http\Controllers\NoteController::class, 'store']);
    Route::delete('/notes/delete/{id}', [App\Http\Controllers\NoteController::class, 'destroy']);
});