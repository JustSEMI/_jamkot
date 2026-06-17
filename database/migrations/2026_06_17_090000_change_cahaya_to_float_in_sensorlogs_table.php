<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Ubah kolom 'cahaya' dari integer (persentase 0-100)
     * menjadi float untuk menyimpan nilai lux yang akurat (0 - 100.000+)
     */
    public function up(): void
    {
        Schema::table('sensorlogs', function (Blueprint $table) {
            $table->float('cahaya')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sensorlogs', function (Blueprint $table) {
            $table->integer('cahaya')->change();
        });
    }
};
