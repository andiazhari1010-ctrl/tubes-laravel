<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pomodoro_logs', function (Blueprint $table) {
            $table->id();
            // Kita hapus user_id dulu biar simpel
            // $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // pomodoro, shortBreak, etc
            $table->timestamp('ended_at');
            $table->integer('duration_seconds');
            $table->string('status')->nullable(); // completed / skipped
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pomodoro_logs');
    }
};