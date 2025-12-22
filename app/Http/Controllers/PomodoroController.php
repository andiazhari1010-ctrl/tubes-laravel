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
        $user_id = Auth::id();
        $today = now()->startOfDay();

        // 1. Total Detik (Gabungan Completed + Skipped)
        // Kita hitung semua durasi yang tercatat hari ini
        $totalSeconds = PomodoroLog::where('user_id', $user_id)
            ->whereDate('created_at', '>=', $today)
            ->where('type', 'pomodoro') 
            ->whereIn('status', ['completed', 'skipped']) // <-- PENTING: Skipped juga dihitung
            ->sum('duration_seconds');

        // 2. Total Sesi
        $totalSessions = PomodoroLog::where('user_id', $user_id)
            ->whereDate('created_at', '>=', $today)
            ->where('type', 'pomodoro')
            ->whereIn('status', ['completed', 'skipped'])
            ->count();

        // 3. Chart Data (Kirim dalam DETIK, bukan menit)
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $seconds = PomodoroLog::where('user_id', $user_id)
                ->whereDate('created_at', $date)
                ->where('type', 'pomodoro')
                ->sum('duration_seconds');
            
            $chartData[] = [
                'date' => now()->subDays($i)->format('D'),
                'seconds' => (int)$seconds // Pastikan labelnya 'seconds'
            ];
        }

        // 4. Ranking
        $ranking = PomodoroLog::select('user_id', DB::raw('SUM(duration_seconds) as total_seconds'))
            ->where('type', 'pomodoro')
            ->groupBy('user_id')
            ->orderByDesc('total_seconds')
            ->limit(5)
            ->with('user:id,name')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->user->name ?? 'Unknown',
                    'time' => gmdate("H:i:s", $item->total_seconds)
                ];
            });

        return response()->json([
            'total_seconds' => (int)$totalSeconds,
            'total_sessions' => $totalSessions,
            'chart_data' => $chartData,
            'ranking' => $ranking
        ]);
    }
}