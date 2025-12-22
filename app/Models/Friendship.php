<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friendship extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Relasi ke Pengirim
    public function sender()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke Penerima
    public function receiver()
    {
        return $this->belongsTo(User::class, 'friend_id');
    }
}