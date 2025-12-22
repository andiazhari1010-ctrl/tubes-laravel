<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Notification; // Tambah ini
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index() {
        return response()->json(Event::where('user_id', Auth::id())->get());
    }

    public function store(Request $request) {
        $request->validate([
            'title' => 'required|max:50',
            'event_date' => 'required|date_format:Y-m-d',
        ]);

        $isCollab = $request->has('friend_id') && !empty($request->friend_id);
        $color = $isCollab ? '#42a5f5' : '#ef5350'; // Biru jika collab

        // 1. Simpan Event untuk Diri Sendiri (User A)
        Event::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'event_date' => $request->event_date,
            'color' => $color
        ]);

        // 2. Jika Collab: KIRIM NOTIFIKASI INVITE ke Teman (User B)
        if ($isCollab) {
            Notification::create([
                'user_id' => $request->friend_id,
                'title' => 'Undangan Kegiatan',
                'message' => Auth::user()->name . " mengajak kegiatan: " . $request->title . " pada tanggal " . $request->event_date,
                'type' => 'invite', // Tandai ini undangan
                'data' => json_encode([ // Simpan data mentah buat nanti diproses
                    'title' => $request->title . " (with " . Auth::user()->name . ")",
                    'event_date' => $request->event_date,
                    'sender_id' => Auth::id(),
                    'sender_name' => Auth::user()->name
                ]),
                'link' => '#' // Link dummy
            ]);
        }

        return response()->json(['message' => 'Agenda Saved! Invite sent.']);
    }

    public function destroy($id) {
        $event = Event::where('id', $id)->where('user_id', Auth::id())->first();
        if($event) { $event->delete(); return response()->json(['message' => 'Deleted']); }
        return response()->json(['message' => 'Not found'], 404);
    }
}