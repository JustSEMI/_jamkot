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
    Schema::create('sensorlogs', function (Blueprint $table) {
        $table->id();
        $table->string('sensor_id');
        $table->float('suhu');
        $table->float('kelembapan');
        $table->integer('cahaya');
        $table->enum('pompa_status', ['ON', 'OFF'])->default('OFF');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensorlogs');
    }
};
