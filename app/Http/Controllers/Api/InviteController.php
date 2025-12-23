<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification; // Asumsi invite disimpan di tabel notifications
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class InviteController extends Controller
{
    // Fungsi untuk menangani Accept/Reject dari tombol Notifikasi
    public function respond(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:accept,reject' // Hanya terima 'accept' atau 'reject'
        ]);

        // 1. Cari Notifikasi berdasarkan ID
        $notification = Notification::where('id', $id)
                                    ->where('user_id', Auth::id())
                                    ->first();

        if (!$notification) {
            return response()->json(['message' => 'Invitation not found'], 404);
        }

        // 2. Cek Action User
        if ($request->action === 'accept') {
            
            // Logika Terima: Masukkan event ke kalender user ini
            // (Asumsi di data notifikasi ada 'event_id' atau 'data' JSON)
            
            // Contoh: Jika notifikasi menyimpan ID event di kolom 'related_id'
            $eventId = $notification->related_id; 
            
            if($eventId) {
                // Attach user ke event (pivot table)
                // Pastikan model Event punya relasi 'participants'
                $event = Event::find($eventId);
                if($event) {
                    // Cek biar ga double entry
                    if(!$event->participants()->where('user_id', Auth::id())->exists()){
                        $event->participants()->attach(Auth::id(), ['status' => 'accepted']);
                    }
                }
            }

            $message = 'Invitation Accepted! Event added to calendar.';
            
            // Tandai notifikasi sudah dibaca/selesai
            $notification->is_read = true; 
            $notification->save();

        } else {
            // Logika Tolak: Hapus notifikasi atau tandai rejected
            $notification->delete(); // Atau $notification->update(['status' => 'rejected']);
            $message = 'Invitation Rejected.';
        }

        return response()->json([
            'message' => $message,
            'status' => 'success'
        ], 200);
    }
}