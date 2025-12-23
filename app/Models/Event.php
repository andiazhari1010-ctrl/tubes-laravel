<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $guarded = ['id'];

    /**
     * Relasi: Event punya banyak Peserta (User)
     * Ini wajib ada supaya tombol Accept bisa jalan.
     */
    public function participants()
    {
        // Hubungkan ke tabel pivot 'event_participants'
        // withPivot('status') berguna untuk menyimpan status 'accepted'/'rejected'
        return $this->belongsToMany(User::class, 'event_participants', 'event_id', 'user_id')
                    ->withPivot('status')
                    ->withTimestamps();
    }

    /**
     * Relasi: Siapa pembuat event ini? (Owner)
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}