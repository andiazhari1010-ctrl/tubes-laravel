<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class InviteController extends Controller
{
    public function respond(Request $request, $id) { // $id adalah ID Notifikasi
        $notif = Notification::where('id', $id)->where('user_id', Auth::id())->first();
        
        if (!$notif || $notif->type != 'invite') {
            return response()->json(['message' => 'Invalid Invite'], 400);
        }

        $action = $request->action; // 'accept' atau 'reject'
        $data = json_decode($notif->data); // Ambil data event dari JSON

        if ($action == 'accept') {
            // 1. Buat Event untuk User B (Penerima)
            Event::create([
                'user_id' => Auth::id(),
                'title' => $data->title,
                'event_date' => $data->event_date,
                'color' => '#42a5f5' // Biru
            ]);

            // 2. Ubah Notif jadi Info biasa (biar gak bisa diklik lagi)
            $notif->update([
                'type' => 'info', 
                'title' => 'Undangan Diterima',
                'message' => 'Anda menerima undangan dari ' . $data->sender_name,
                'is_read' => true
            ]);

            // 3. Kirim Notif Balik ke Pengirim (User A)
            Notification::create([
                'user_id' => $data->sender_id,
                'title' => 'Undangan Diterima!',
                'message' => Auth::user()->name . " menerima ajakan kegiatan tanggal " . $data->event_date,
                'type' => 'info'
            ]);

        } else {
            // KALAU DITOLAK
            $notif->update(['type' => 'info', 'title' => 'Undangan Ditolak', 'is_read' => true]);

            // Kirim Notif Balik ke Pengirim
            Notification::create([
                'user_id' => $data->sender_id,
                'title' => 'Undangan Ditolak',
                'message' => Auth::user()->name . " menolak kegiatan tanggal " . $data->event_date,
                'type' => 'info'
            ]);
        }

        return response()->json(['message' => 'Response Sent!']);
    }
}