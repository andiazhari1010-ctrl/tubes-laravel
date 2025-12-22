<?php

namespace App\Models;

// ... imports yang sudah ada ...
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Friendship; // <--- PASTIKAN ADA BARIS INI DI ATAS

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // ==========================================
    // TAMBAHKAN KODE INI DI BAGIAN BAWAH
    // ==========================================

    // Relasi 1: Teman yang SAYA ajak (Saya sebagai Sender)
    // Controller memanggil ini dengan nama 'friendshipsTo'
    public function friendshipsTo()
    {
        return $this->hasMany(Friendship::class, 'user_id');
    }

    // Relasi 2: Teman yang MENG-AJAK saya (Saya sebagai Receiver)
    // Controller memanggil ini dengan nama 'friendshipsOf' <--- INI PENYEBAB ERROR 500 KEMARIN
    public function friendshipsOf()
    {
        return $this->hasMany(Friendship::class, 'friend_id');
    }
}