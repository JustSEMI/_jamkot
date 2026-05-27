<?php

namespace Database\Seeders;

use App\Models\Schedule;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        if (User::where('username', 'keju')->count() === 0) {
            User::factory()->create([
                'username' => 'keju',
                'email' => 'keju@chizui.dev',
                'role' => 'admin',
            ]);
        }

        if (Schedule::count() === 0) {
            $schedule = new Schedule;
            $schedule->save();
        }
    }
}
