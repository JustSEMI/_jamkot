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
        Schema::table('user', function (Blueprint $table) {
            $table->enum('role', ['admin', 'user'])->default('user')->after('email');
            $table->boolean('can_panel')->default(true)->after('role');
            $table->boolean('can_analisis')->default(true)->after('can_panel');
            $table->boolean('can_schedule')->default(true)->after('can_analisis');
            $table->boolean('can_view3d')->default(true)->after('can_schedule');
            $table->boolean('can_settings')->default(false)->after('can_view3d');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user', function (Blueprint $table) {
            $table->dropColumn(['role', 'can_panel', 'can_analisis', 'can_schedule', 'can_view3d', 'can_settings']);
        });
    }
};
