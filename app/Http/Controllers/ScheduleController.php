<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreScheduleRequest;
use App\Models\Schedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    /**
     * Display watering schedules.
     */
    public function index(): View
    {
        $schedule = Schedule::first() ?? new Schedule;

        return view('schedule.index', compact('schedule'));
    }

    /**
     * Store or update watering schedules.
     */
    public function store(StoreScheduleRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Schedule::updateOrCreate(
            ['id' => 1],
            [
                'pagi_mulai' => $validated['jadwal_pagi_mulai'],
                'pagi_selesai' => $validated['jadwal_pagi_selesai'],
                'siang_mulai' => $validated['jadwal_siang_mulai'],
                'siang_selesai' => $validated['jadwal_siang_selesai'],
                'sore_mulai' => $validated['jadwal_sore_mulai'],
                'sore_selesai' => $validated['jadwal_sore_selesai'],
                'batas_kelembapan' => $validated['batas_kelembapan'],
            ]
        );

        return back()->with('sukses', 'JADWAL BERHASIL DISIMPAN');
    }
}
