<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menyimpan status kesehatan perangkat ESP32 (satu baris per device_id).
     * Di-upsert setiap kali ESP32 mengirim heartbeat, bukan di-insert terus.
     */
    public function up(): void
    {
        Schema::create('device_status', function (Blueprint $table) {
            $table->id();
            $table->string('device_id')->unique();
            $table->string('ip_address')->nullable();
            $table->unsignedBigInteger('uptime_seconds')->default(0);
            $table->boolean('dht_connected')->default(false);
            $table->boolean('ldr_connected')->default(false);
            $table->unsignedInteger('free_heap')->nullable()->comment('Memori bebas ESP32 dalam bytes');
            $table->integer('rssi')->nullable()->comment('Kekuatan sinyal WiFi dalam dBm');
            $table->timestamp('last_seen_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_status');
    }
};
