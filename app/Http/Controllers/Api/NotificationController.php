<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller {
    public function index() {
        return Notification::where('user_id', Auth::id())->orderBy('created_at', 'desc')->get();
    }
    public function markRead($id) {
        Notification::where('id', $id)->where('user_id', Auth::id())->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }
}