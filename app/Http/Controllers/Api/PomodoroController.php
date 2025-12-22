<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PomodoroLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PomodoroController extends Controller
{
    public function storeLog(Request $request)
    {
        // Validasi User Login
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $log = new PomodoroLog();
        $log->user_id = Auth::id();
        $log->type = $request->type;
        $log->duration_seconds = $request->duration; 
        $log->status = $request->status;
        $log->ended_at = now();
        $log->save();

        return response()->json(['message' => 'Saved']);
    }

    public function getStats()
    {
        // 1. Cek Login
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user_id = Auth::id();
        
        // 2. Ambil Semua Data Hari Ini (Tanpa Filter Jam yang Ketat)
        // Kita pakai created_at >= hari ini jam 00:00
        $today = now()->startOfDay(); 

        // TOTAL DETIK
        $totalSeconds = PomodoroLog::where('user_id', $user_id)
            ->where('created_at', '>=', $today)
            // Hapus filter 'type' sementara untuk tes, biar semua masuk
            // ->where('type', 'pomodoro') 
            ->sum('duration_seconds');

        // TOTAL SESI
        $totalSessions = PomodoroLog::where('user_id', $user_id)
            ->where('created_at', '>=', $today)
            ->count();

        // CHART DATA (Sederhana)
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $dStart = now()->subDays($i)->startOfDay();
            $dEnd = now()->subDays($i)->endOfDay();
            
            $sec = PomodoroLog::where('user_id', $user_id)
                ->whereBetween('created_at', [$dStart, $dEnd])
                ->sum('duration_seconds');
            
            $chartData[] = [
                'date' => now()->subDays($i)->format('D'),
                'seconds' => (int)$sec
            ];
        }

        // RANKING (Tanpa Error Relation)
        // Jika Model User belum di-set relasinya, kode ini akan kita buat 'fail-safe'
        try {
            $ranking = PomodoroLog::select('user_id', DB::raw('SUM(duration_seconds) as total_seconds'))
                ->groupBy('user_id')
                ->orderByDesc('total_seconds')
                ->limit(5)
                ->with('user:id,name') // Ini butuh Model PomodoroLog punya func user()
                ->get()
                ->map(function ($item) {
                    return [
                        'name' => $item->user->name ?? 'User #' . $item->user_id,
                        'time' => gmdate("H:i:s", $item->total_seconds)
                    ];
                });
        } catch (\Exception $e) {
            // Jika error relasi, kirim array kosong dulu biar gak crash
            $ranking = [];
        }

        return response()->json([
            'total_seconds' => (int)$totalSeconds,
            'total_sessions' => $totalSessions,
            'chart_data' => $chartData,
            'ranking' => $ranking
        ]);
    }
}