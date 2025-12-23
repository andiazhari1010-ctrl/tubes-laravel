<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FriendController extends Controller
{
    /**
     * Menampilkan halaman Add Friend.
     * Logika data (list teman, request, accept) diambil via API (JavaScript).
     */
    public function index()
    {
        return view('add-friend');
    }
}