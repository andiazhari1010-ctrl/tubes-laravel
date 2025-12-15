<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PomodoroLog;     // Pastikan Model ini ada
use App\Models\PomodoroSetting; // Pastikan Model ini ada

class PomodoroController extends Controller
{
    /**
     * Mengambil konfigurasi waktu (Get Config).
     * Dipanggil saat halaman Pomodoro dibuka.
     */
    public function getConfig()
    {
        // Coba ambil settingan pertama di database
        // Jika tidak ada, buat settingan default (25/5/15 menit)
        $setting = PomodoroSetting::firstOrCreate([], [
            'pomodoro_duration' => 25,
            'short_break_duration' => 5,
            'long_break_duration' => 15
        ]);

        // Kirim data ke Javascript dalam bentuk JSON
        return response()->json($setting);
    }

    /**
     * Menyimpan riwayat sesi (Store Log).
     * Dipanggil saat timer selesai atau di-skip.
     */
    public function storeLog(Request $request)
    {
        // Validasi data yang dikirim dari JS (Opsional tapi bagus)
        $request->validate([
            'type' => 'required|string',
            'duration' => 'required|integer',
        ]);

        // Simpan ke Tabel 'pomodoro_logs'
        $log = new PomodoroLog();
        $log->type = $request->type;             // 'pomodoro', 'shortBreak', atau 'longBreak'
        $log->duration_seconds = $request->duration; // Durasi dalam detik
        
        // PENTING: Jika di migration kamu ada kolom 'status', uncomment baris bawah ini:
        // $log->status = $request->status ?? 'completed'; 
        
        $log->ended_at = now();                  // Waktu selesai sekarang
        $log->save();

        return response()->json([
            'message' => 'Sesi berhasil disimpan!',
            'data' => $log
        ]);
    }

    /**
     * Mengambil statistik harian (Total Menit & Jumlah Sesi)
     */
    public function getStats()
    {
        // 1. Tentukan waktu hari ini (mulai jam 00:00)
        $today = now()->startOfDay();

        // 2. Hitung total detik fokus hari ini (Hanya tipe 'pomodoro')
        $totalSeconds = PomodoroLog::where('created_at', '>=', $today)
            ->where('type', 'pomodoro') // Hanya hitung waktu fokus, bukan istirahat
            ->sum('duration_seconds');

        // 3. Hitung berapa kali sesi selesai hari ini
        $totalSessions = PomodoroLog::where('created_at', '>=', $today)
            ->where('type', 'pomodoro')
            ->count();

        // 4. Kirim data ke frontend
        return response()->json([
            'total_minutes' => round($totalSeconds / 60), // Ubah ke menit
            'total_sessions' => $totalSessions
        ]);
    }
}