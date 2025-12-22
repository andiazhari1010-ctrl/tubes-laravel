<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FriendController extends Controller
{
    // Tugasnya cuma satu: Buka halaman add-friend.blade.php
    public function index()
    {
        return view('add-friend');
    }
}