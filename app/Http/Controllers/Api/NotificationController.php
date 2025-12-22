<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // 1. API: Ambil data untuk Dropdown (Limit 10)
    public function index() {
        $notifs = Notification::where('user_id', Auth::id())
                    ->orderBy('created_at', 'desc')
                    ->limit(10) 
                    ->get();
        return response()->json($notifs);
    }

    // 2. API: Tandai sudah dibaca
    public function markRead($id) {
        $notif = Notification::where('id', $id)->where('user_id', Auth::id())->first();
        if ($notif) {
            $notif->update(['is_read' => true]);
        }
        return response()->json(['success' => true]);
    }

    // 3. VIEW: Halaman History Lengkap
    public function listAll() {
        // Ambil SEMUA notifikasi
        $notifs = Notification::where('user_id', Auth::id())
                    ->orderBy('created_at', 'desc')
                    ->get();

        // PENTING: Di sini pakai 'notifications' (sesuai nama file blade kamu)
        return view('notifications', compact('notifs'));
    }
}