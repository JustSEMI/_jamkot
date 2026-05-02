<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;

class ScheduleController extends Controller
{
    public function index()
    {
        // Ambil data jadwal pertama, kalau ga ada kasih data kosong
        $schedule = Schedule::first() ?? new Schedule();
        return view('jadwal', compact('schedule'));
    }

    public function store(Request $request)
    {
        Schedule::updateOrCreate(
            ['id' => 1],
            [
                'pagi_mulai' => $request->jadwal_pagi_mulai,
                'pagi_selesai' => $request->jadwal_pagi_selesai,
                'siang_mulai' => $request->jadwal_siang_mulai,
                'siang_selesai' => $request->jadwal_siang_selesai,
                'sore_mulai' => $request->jadwal_sore_mulai,
                'sore_selesai' => $request->jadwal_sore_selesai,
                'batas_kelembapan' => $request->batas_kelembapan,
            ]
        );

        return redirect()->back()->with('success', 'Jadwal berhasil diperbarui');
    }
}