<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Friendship;
use App\Models\Notification; // <--- PENTING: Import Model Notification
use Illuminate\Support\Facades\Auth;

class FriendController extends Controller
{
    // ==========================================
    // 1. REQUEST PERTEMANAN (ADD FRIEND)
    // ==========================================
    public function addFriend(Request $request) {
        $friend_id = $request->friend_id;
        $user_id = Auth::id(); // Saya (Pengirim)

        if($user_id == $friend_id) {
            return response()->json(['message' => 'Cannot add yourself'], 400);
        }

        // Cek apakah sudah berteman / request pending
        $exists = Friendship::where(function($q) use($user_id, $friend_id){
            $q->where('user_id', $user_id)->where('friend_id', $friend_id);
        })->orWhere(function($q) use($user_id, $friend_id){
            $q->where('user_id', $friend_id)->where('friend_id', $user_id);
        })->exists();

        if($exists) {
            return response()->json(['message' => 'Request already sent or already friends'], 400);
        }

        // 1. Buat Data Friendship (Pending)
        Friendship::create([
            'user_id' => $user_id,     // Pengirim
            'friend_id' => $friend_id, // Penerima
            'status' => 'pending'
        ]);

        // 2. [BARU] Buat Notifikasi untuk Teman yang di-Add
        // Agar muncul di "Inbox" atau lonceng notifikasi dia
        Notification::create([
            'user_id' => $friend_id, // Kirim ke teman
            'title'   => 'New Friend Request',
            'message' => Auth::user()->name . ' wants to be your friend.',
            'type'    => 'friend_request',
            'data'    => json_encode(['sender_id' => $user_id]),
            'is_read' => false
        ]);

        return response()->json(['message' => 'Request sent']);
    }

    // ==========================================
    // 2. CEK REQUEST MASUK (INBOX)
    // ==========================================
    public function getIncomingRequests() {
        $myId = Auth::id();

        // Cari siapa yang add saya tapi status masih pending
        $senderIds = Friendship::where('friend_id', $myId)
                        ->where('status', 'pending')
                        ->pluck('user_id');
        
        $requests = User::whereIn('id', $senderIds)->get();

        return response()->json($requests);
    }

    // ==========================================
    // 3. LIST TEMAN SAYA (ACCEPTED)
    // ==========================================
    public function getMyFriends() {
        $myId = Auth::id();

        // Teman dari arah: Saya add Dia
        $friends1 = Friendship::where('user_id', $myId)
                        ->where('status', 'accepted')
                        ->pluck('friend_id');

        // Teman dari arah: Dia add Saya
        $friends2 = Friendship::where('friend_id', $myId)
                        ->where('status', 'accepted')
                        ->pluck('user_id');

        $friendIds = $friends1->merge($friends2);
        $friends = User::whereIn('id', $friendIds)->get();

        return response()->json($friends);
    }

    // ==========================================
    // 4. TERIMA PERTEMANAN (ACCEPT)
    // ==========================================
    public function acceptFriend($sender_id) {
        $myId = Auth::id();
        
        // Cari request pending dari dia ke saya
        $friendship = Friendship::where('user_id', $sender_id)
                        ->where('friend_id', $myId)
                        ->where('status', 'pending')
                        ->first();

        if ($friendship) {
            // Update jadi accepted
            $friendship->update(['status' => 'accepted']);

            // [BARU] Kirim Notifikasi Balik ke Pengirim bahwa request diterima
            Notification::create([
                'user_id' => $sender_id, // Kirim ke orang yang dulu nge-add saya
                'title'   => 'Request Accepted',
                'message' => Auth::user()->name . ' accepted your friend request.',
                'type'    => 'info',
                'data'    => json_encode(['friend_id' => $myId]),
                'is_read' => false
            ]);

            return response()->json(['message' => 'Accepted']);
        }

        return response()->json(['message' => 'Request not found'], 404);
    }

    // ==========================================
    // 5. TOLAK PERTEMANAN (REJECT)
    // ==========================================
    public function rejectFriend($sender_id) {
        $myId = Auth::id();
        
        $deleted = Friendship::where('user_id', $sender_id)
            ->where('friend_id', $myId)
            ->where('status', 'pending')
            ->delete();

        if ($deleted) {
            return response()->json(['message' => 'Rejected']);
        }
        
        return response()->json(['message' => 'Request not found'], 404);
    }

    // ==========================================
    // 6. UNFRIEND
    // ==========================================
    public function unfriend(Request $request) {
        $myId = Auth::id();
        $friendId = $request->friend_id;

        Friendship::where(function($q) use($myId, $friendId){
            $q->where('user_id', $myId)->where('friend_id', $friendId);
        })->orWhere(function($q) use($myId, $friendId){
            $q->where('user_id', $friendId)->where('friend_id', $myId);
        })->delete();

        return response()->json(['message' => 'Unfriended successfully']);
    }
}