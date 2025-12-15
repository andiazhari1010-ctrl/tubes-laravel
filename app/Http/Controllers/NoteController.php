<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Note;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    // 1. READ (Ambil semua catatan milik user yg login)
    public function index()
    {
        $notes = Note::where('user_id', Auth::id())
                    ->orderBy('updated_at', 'desc')
                    ->get();
        return response()->json($notes);
    }

    // 2. CREATE & UPDATE (Simpan Catatan)
    public function store(Request $request)
    {
        // Validasi input
        $request->validate(['content' => 'required']);

        // Ambil data konten dengan cara yang aman (Fix error garis merah)
        $content = $request->input('content'); 
        $id = $request->input('id');

        // Cek apakah ini Update (ada ID) atau Create baru
        if ($id) {
            $note = Note::where('id', $id)->where('user_id', Auth::id())->first();
            if ($note) {
                // Update menggunakan variabel $content
                $note->update(['content' => $content]);
                return response()->json(['message' => 'Updated!', 'data' => $note]);
            }
        } else {
            // Buat Baru menggunakan variabel $content
            $note = Note::create([
                'user_id' => Auth::id(),
                'content' => $content
            ]);
            return response()->json(['message' => 'Saved!', 'data' => $note]);
        }
        
        return response()->json(['message' => 'Error'], 400);
    }

    // 3. DELETE (Hapus Catatan)
    public function destroy($id)
    {
        $note = Note::where('id', $id)->where('user_id', Auth::id())->first();
        if ($note) {
            $note->delete();
            return response()->json(['message' => 'Deleted!']);
        }
        return response()->json(['message' => 'Not found or Unauthorized'], 403);
    }
}