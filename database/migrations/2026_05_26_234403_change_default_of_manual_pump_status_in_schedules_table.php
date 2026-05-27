<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing records from AUTO to OFF
        DB::table('schedules')->where('manual_pump_status', 'AUTO')->update(['manual_pump_status' => 'OFF']);

        Schema::table('schedules', function (Blueprint $table) {
            $table->enum('manual_pump_status', ['ON', 'OFF'])->default('OFF')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->enum('manual_pump_status', ['AUTO', 'ON', 'OFF'])->default('AUTO')->change();
        });
    }
};
