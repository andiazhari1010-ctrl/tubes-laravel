<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('notifications', function (Blueprint $table) {
        $table->string('type')->default('info'); // 'info' atau 'invite'
        $table->text('data')->nullable(); // Simpan JSON data event (title, date, sender_id)
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            //
        });
    }
};
