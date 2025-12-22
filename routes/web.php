<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController; 
use App\Http\Controllers\NoteController; // Pastikan Controller Notes di-import
use App\Http\Controllers\Api\PomodoroController; // [BARU] Import Controller Pomodoro

// --- HALAMAN PUBLIC (TIDAK BUTUH LOGIN) ---

// Halaman Depan
Route::get('/', function () { return view('home'); });

// Authentication Routes
Route::get('/login', function () { return view('login'); })->name('login');
Route::get('/register', function () { return view('register'); });

// Proses Form Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout']);


// --- HALAMAN YANG BUTUH LOGIN (DIPROTEKSI MIDDLEWARE) ---

Route::middleware('auth')->group(function () {
    
    // 1. Menu Navigasi Utama
    Route::get('/index', function () { return view('index'); });
    Route::get('/pomodoro', function () { return view('pomodoro'); });
    Route::get('/calendar', function () { return view('calendar'); });
    Route::get('/notes', function () { return view('notes'); });
    Route::get('/notification', function () { return view('notification'); });
    Route::get('/add-friend', function () { return view('add-friend'); });
    Route::get('/info', function () { return view('info'); });

    // 2. Fitur Notes
    Route::get('/notes/data', [NoteController::class, 'index']);
    Route::post('/notes/save', [NoteController::class, 'store']);
    Route::delete('/notes/delete/{id}', [NoteController::class, 'destroy']);

    // 3. Fitur Pomodoro (API Internal) - [PENTING: INI PERBAIKANNYA]
    // Ditaruh di sini agar bisa membaca Session User yang sedang login (Auth::id())
    Route::post('/api/log', [PomodoroController::class, 'storeLog']);
    Route::get('/api/stats', [PomodoroController::class, 'getStats']);

});