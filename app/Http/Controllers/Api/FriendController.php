<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Friendship;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class FriendController extends Controller {
    // Kirim Request
    public function addFriend(Request $request) {
        $user_id = Auth::id();
        $friend_id = $request->friend_id;

        // Cek duplikat
        $exists = Friendship::where(function($q) use ($user_id, $friend_id) {
            $q->where('user_id', $user_id)->where('friend_id', $friend_id);
        })->orWhere(function($q) use ($user_id, $friend_id) {
            $q->where('user_id', $friend_id)->where('friend_id', $user_id);
        })->exists();

        if($exists) return response()->json(['message' => 'Sudah ada request/berteman'], 400);

        Friendship::create(['user_id' => $user_id, 'friend_id' => $friend_id, 'status' => 'pending']);
        
        // Kirim Notif
        Notification::create([
            'user_id' => $friend_id,
            'title' => 'Permintaan Pertemanan',
            'message' => Auth::user()->name . ' ingin berteman!',
            'link' => '/add-friend'
        ]);
        
        return response()->json(['message' => 'Request terkirim!']);
    }

    // List Request Masuk
    public function getIncomingRequests() {
        return Friendship::where('friend_id', Auth::id())
            ->where('status', 'pending')
            ->join('users', 'users.id', '=', 'friendships.user_id')
            ->select('friendships.id', 'users.name', 'users.email')
            ->get();
    }

    // List Teman
    public function getMyFriends() {
        $userId = Auth::id();
        // Ambil pertemanan ACCEPTED (baik sebagai pengirim atau penerima)
        $friends1 = Friendship::where('user_id', $userId)->where('status', 'accepted')->join('users', 'users.id', '=', 'friendships.friend_id')->select('users.name', 'users.email');
        $friends2 = Friendship::where('friend_id', $userId)->where('status', 'accepted')->join('users', 'users.id', '=', 'friendships.user_id')->select('users.name', 'users.email');
        
        return $friends1->union($friends2)->get();
    }

    // Terima Teman
    public function acceptFriend($id) {
        Friendship::where('id', $id)->where('friend_id', Auth::id())->update(['status' => 'accepted']);
        return response()->json(['message' => 'Diterima']);
    }

    // Tolak Teman
    public function rejectFriend($id) {
        Friendship::where('id', $id)->where('friend_id', Auth::id())->delete();
        return response()->json(['message' => 'Ditolak']);
    }
}