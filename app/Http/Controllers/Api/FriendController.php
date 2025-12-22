<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Friendship; // <--- Pastikan Model Friendship di-import!
use Illuminate\Support\Facades\Auth;

class FriendController extends Controller
{
    // API: Tambah Teman
    public function addFriend(Request $request) {
        $friend_id = $request->friend_id;
        $user_id = Auth::id();

        if($user_id == $friend_id) return response()->json(['message' => 'Cannot add yourself'], 400);

        $exists = Friendship::where(function($q) use($user_id, $friend_id){
            $q->where('user_id', $user_id)->where('friend_id', $friend_id);
        })->orWhere(function($q) use($user_id, $friend_id){
            $q->where('user_id', $friend_id)->where('friend_id', $user_id);
        })->exists();

        if($exists) return response()->json(['message' => 'Request already sent or already friends'], 400);

        Friendship::create([
            'user_id' => $user_id,
            'friend_id' => $friend_id,
            'status' => 'pending'
        ]);

        return response()->json(['message' => 'Request sent']);
    }

    // API: Cek Request Masuk
    public function getIncomingRequests() {
        $requests = User::whereHas('friendshipsOf', function($q) {
            $q->where('friend_id', Auth::id())->where('status', 'pending');
        })->get();
        return response()->json($requests);
    }

    // API: List Teman (Accepted) -> INI YANG DIPAKAI "LOADING FRIENDS"
    // API: List Teman (FIXED: Exclude Self)
    // API: List Teman (VERSI FIXED: Exclude Self & Bidirectional)
    public function getMyFriends() {
        $myId = Auth::id();

        // 1. Ambil ID teman dari relasi dimana saya sebagai PENGIRIM (user_id)
        $friends1 = Friendship::where('user_id', $myId)
                        ->where('status', 'accepted')
                        ->pluck('friend_id'); // Ambil kolom friend_id (Orang lain)

        // 2. Ambil ID teman dari relasi dimana saya sebagai PENERIMA (friend_id)
        $friends2 = Friendship::where('friend_id', $myId)
                        ->where('status', 'accepted')
                        ->pluck('user_id'); // Ambil kolom user_id (Orang lain)

        // 3. Gabungkan kedua list ID tersebut (Merge)
        $friendIds = $friends1->merge($friends2);

        // 4. Ambil Data User berdasarkan ID yang sudah dikumpulkan tadi
        // whereIn secara otomatis akan mengambil user sesuai list ID
        $friends = User::whereIn('id', $friendIds)->get();

        return response()->json($friends);
    }

    // API: Terima Teman
    public function acceptFriend($id) {
        // Update status jadi accepted
        Friendship::where('id', $id)->update(['status' => 'accepted']); // Cara cepat update by ID request
        return response()->json(['message' => 'Accepted']);
    }

    // API: Tolak Teman
    public function rejectFriend($id) {
        Friendship::where('id', $id)->delete();
        return response()->json(['message' => 'Rejected']);
    }

    // ... (kode sebelumnya) ...

    // API: Unfriend (Hapus Pertemanan)
    public function unfriend(Request $request) {
        $myId = Auth::id();
        $friendId = $request->friend_id;

        // Cari hubungan pertemanan (bolak-balik) dan hapus
        Friendship::where(function($q) use($myId, $friendId){
            $q->where('user_id', $myId)->where('friend_id', $friendId);
        })->orWhere(function($q) use($myId, $friendId){
            $q->where('user_id', $friendId)->where('friend_id', $myId);
        })->delete();

        return response()->json(['message' => 'Unfriended successfully']);
    }
}

