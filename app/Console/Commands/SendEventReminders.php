<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;
use App\Models\Notification;
use Carbon\Carbon;

class SendEventReminders extends Command
{
    protected $signature = 'events:remind';
    protected $description = 'Kirim notifikasi untuk event besok';

    public function handle()
    {
        // Cari Tanggal Besok
        $tomorrow = Carbon::tomorrow()->format('Y-m-d');
        
        // Ambil semua event yang tanggalnya besok
        $events = Event::whereDate('event_date', $tomorrow)->get();

        foreach ($events as $event) {
            // Cek duplikat notif (biar gak spam kalau command dijalankan 2x)
            $exists = Notification::where('user_id', $event->user_id)
                        ->where('title', 'Upcoming Event')
                        ->where('message', 'like', "%$event->title%")
                        ->exists();

            if (!$exists) {
                Notification::create([
                    'user_id' => $event->user_id,
                    'title' => 'Upcoming Event',
                    'message' => "Besok: " . $event->title,
                    'link' => '/calendar'
                ]);
            }
        }
        $this->info('Reminders sent successfully!');
    }
}