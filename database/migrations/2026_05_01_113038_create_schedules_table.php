<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            // Jadwal Pagi
            $table->time('pagi_mulai')->default('08:00');
            $table->time('pagi_selesai')->default('08:05');
            
            // Jadwal Siang
            $table->time('siang_mulai')->default('12:00');
            $table->time('siang_selesai')->default('12:05');
            
            // Jadwal Sore
            $table->time('sore_mulai')->default('16:00');
            $table->time('sore_selesai')->default('16:05');
            
            // Smart Backup
            $table->integer('batas_kelembapan')->default(80);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
