<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Friendship; // Pastikan baris ini tetap ada

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
    'name',
    'username', // <--- Pastikan ini ada!
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
    // RELASI FRIENDSHIP (JANGAN DIHAPUS)
    // ==========================================

    // Relasi 1: Teman yang SAYA ajak (Saya sebagai Sender)
    public function friendshipsTo()
    {
        return $this->hasMany(Friendship::class, 'user_id');
    }

    // Relasi 2: Teman yang MENG-AJAK saya (Saya sebagai Receiver)
    public function friendshipsOf()
    {
        return $this->hasMany(Friendship::class, 'friend_id');
    }
}