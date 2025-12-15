<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PomodoroController;

// Route untuk mengambil settingan waktu
Route::get('/config', [PomodoroController::class, 'getConfig']);

// Route untuk menyimpan log sesi
Route::post('/log', [PomodoroController::class, 'storeLog']);

// Route untuk mengambil statistik
Route::get('/stats', [PomodoroController::class, 'getStats']);