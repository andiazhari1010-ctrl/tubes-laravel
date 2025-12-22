<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PomodoroLog extends Model
{
    use HasFactory;

    /**
     * Menggunakan 'guarded' id berarti SEMUA kolom selain id
     * (seperti user_id, type, duration_seconds, status, ended_at)
     * boleh diisi secara massal. Ini mencegah error "Mass Assignment".
     */
    protected $guarded = ['id'];

    /**
     * RELASI PENTING:
     * Menghubungkan log ini ke tabel User.
     * Tanpa ini, Controller akan error saat memanggil with('user').
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}