<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // PROSES REGISTER
    public function register(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'name' => 'required|string|max:255', // Kita butuh nama asli juga buat sapaan dashboard
            'username' => 'required|string|unique:users,username|alpha_dash',
            'password' => 'required|string|min:6'
        ]);

        // 2. Buat User Baru di Database
        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->username . '@pixel.com', // Email dummy karena form kamu ga ada input email
            'password' => Hash::make($request->password)
        ]);

        // 3. Redirect ke Login dengan Pesan Sukses
        return redirect('/login')->with('success', 'Akun berhasil dibuat! Silakan login.');
    }

    // PROSES LOGIN
    public function login(Request $request)
    {
        // 1. Validasi
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        // 2. Coba Login
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('index');
        }

        // 3. Jika Gagal
        return back()->withErrors([
            'login_error' => 'Username atau password salah!',
        ]);
    }

    // PROSES LOGOUT
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}