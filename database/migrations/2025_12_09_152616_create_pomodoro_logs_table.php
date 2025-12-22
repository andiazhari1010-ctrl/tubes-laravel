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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // <--- PASTIKAN INI ADA
            $table->integer('duration_seconds');
            $table->string('status');
            $table->timestamp('ended_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pomodoro_logs');
    }
};